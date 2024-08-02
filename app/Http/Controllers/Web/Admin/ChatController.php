<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\BaseController;
use App\Http\Requests\Admin\Order\CreateOrderRequest;
use App\Http\Requests\Admin\Order\UpdateOrderRequest;
use App\Base\Constants\Auth\Role as RoleSlug;
use App\Models\Admin\Order;
use App\Base\Constants\Masters\PushEnums;
use App\Models\Chat;
use App\Base\Constants\Auth\Role;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Admin\Driver;
use App\Models\ChatMessage;
use App\Models\Request\Request as RequestModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends BaseController
{
    protected $chat;

    /**
     * ChatController constructor.
     *
     * @param \App\Models\Admin\Chat $chat
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function index(QueryFilterContract $queryFilter)
    {

        $page = trans('pages_names.chat');

        $main_menu = 'manage-chat';
        $sub_menu = '';

        $owner = auth()->user()->owner->owner_unique_id;

        $drivers = Driver::where('owner_id', $owner)->orderBy('created_at', 'asc')->get();
        $driverIds = $drivers->pluck('user_id')->toArray();

        $query = Chat::join('users', 'chat.user_id', '=', 'users.id')
                    ->whereIn('user_id', $driverIds)
                    ->select('chat.*', 'users.name', 'users.profile_picture')
                    ->groupBy('chat.id')
                    ->orderBy('chat.created_at', 'desc');
                    // ->get();

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();
        return view('admin.chat.index', compact('page', 'main_menu', 'sub_menu','results'));

    }

    // public function fetch(QueryFilterContract $queryFilter)
    // {
    //     // if(!access()->hasRole(RoleSlug::SUPER_ADMIN)){
    //     //     $query = $query->where('orders.user_id', auth()->user()->id);
    //     // }

    //     $query = $this->chat->where('from_type', 4)
    //             ->join('users', 'chats.user_id', '=', 'users.id')
    //             ->select('chats.*', 'users.name', 'users.profile_picture', DB::raw('MAX(user_id) as max_id'))
    //             ->groupBy('user_id')
    //             ->orderBy('chats.created_at', 'desc');
    //             // ->get();

    //     $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

    //     return view('admin.chat._chat', compact('results'));
    // }


    public function getById($id)
    {
        $page = trans('pages_names.chat');
        $main_menu = 'manage-chat';
        $sub_menu = '';
        $auth_user = auth()->user()->owner->user_id;

        $user_messages = ChatMessage::where('receiver_id', $auth_user)
                    ->orWhere('sender_id', $auth_user)
                    ->where('chat_id', $id)
                    ->orderBy('created_at', 'asc')
                    ->get();

        // $subquery = DB::table('chats')->select(DB::raw('MAX(id) as max_id'))->groupBy('from_type');
        // $user_messages = $this->chat->select('chats.*', 'users.name', 'users.profile_picture')
        //                     ->join('users', 'chats.user_id', '=', 'users.id')
        //                     ->where('from_type', '4')
        //                     ->whereIn('chats.id', $subquery)
        //                     ->where('receiver_id', $auth_user)
        //                     ->orderBy('chats.created_at', 'desc')
        //                     ->get();

        $unique_receiver_ids = ChatMessage::where('chat_id', $id)->distinct()->pluck('receiver_id')->toArray();
        $userHasMessages = in_array($auth_user, $unique_receiver_ids);

        if (!$userHasMessages) {
            return abort(404);
        }

        return view('admin.chat.create', compact('user_messages','page', 'main_menu', 'sub_menu'));
    }


    public function getConversations(Request $request)
    {
        $auth_user = auth()->user()->owner->user_id;

        $owner = auth()->user()->owner->owner_unique_id;

        $drivers = Driver::where('owner_id', $owner)->orderBy('created_at', 'asc')->get();
        $driverIds = $drivers->pluck('user_id')->toArray();

        // $conversations = Chat::whereIn(['user_id'=> $request->user_id, 'receiver_id'=> $request->user_id])->whereIn('from_type', [3,4])->orderBy('created_at', 'asc')->get();
        $conversations = ChatMessage::where('chat_id', $request->chat_id)
                    ->orderBy('created_at', 'asc')
                    ->get();

        return $conversations;
    }

    public function store(Request $request)
    {
        $from_type = 'is_company';

        // $chatRequest = Chat::create([
        //     'message' => $request->message,
        //     'from_type' => $from_type,
        //     'user_id' => auth()->user()->id,
        //     'receiver_id' => $request->user_id
        // ]);

        // $driverDetail = User::find($chatRequest->user_id);

        // $chats = Chat::whereIn('from_type', [3,4])->orderBy('created_at', 'asc')->get();

        $chatdriver = Chat::where('id', $request->chat_id)->first();
        // if(!$chatAuth){
        //     $chatRequest = Chat::create(['user_id' => auth()->user()->id]);
        // }

        $owner = auth()->user()->owner->owner_unique_id;

        $drivers = Driver::where('owner_id', $owner)->where('user_id', $chatdriver->user_id)->orderBy('created_at', 'asc')->first();
        // $driverIds = $drivers->pluck('user_id')->toArray();
        // dd($drivers);

        // foreach ($driverIds as $driverId) {
        //     $driv = $driverId;
        // }
        $chatMessage = new ChatMessage();
        $chatMessage->chat_id = $chatdriver->id;
        $chatMessage->message = $request->message;
        $chatMessage->sender_id = auth()->user()->id;
        $chatMessage->receiver_id = $drivers->user_id;
        $chatMessage->from_type = $from_type;
        $chatMessage->save();

        $driverDetail = User::find($chatMessage->sender_id);

        $chats = ChatMessage::where('chat_id', $chatMessage->chat_id)->orderBy('created_at', 'asc')->get();
        // $chats = Chat::whereIn('from_type', [3, 4])->orderBy('created_at', 'asc')->get();

        $driver = $driverDetail;
        $notifable_driver = $driver->user;

        foreach ($chats as $key => $chat) {
            if ($chat->from_type == "is_driver") {
                $chats[$key]['message_status'] = 'receive';
            } else {
                $chats[$key]['message_status'] = 'send';
            }
        }


        $socket_data = new \stdClass();
        $socket_data->success = true;
        $socket_data->success_message  = PushEnums::NEW_MESSAGE;
        $socket_data->data = $chats;
        // dispatch(new NotifyViaMqtt('new_message_' . $driver->id, json_encode($socket_data), $driver->id));

        $title = 'New Message From ' . auth()->user()->name;
        $body = $request->message;

        dispatch(new SendPushNotification($notifable_driver,$title,$body));
        return $this->respondSuccess([null => 'message_sent_successfully', 'data' => $chatMessage]);
    }

    public function updateSeen(Request $request){
        $owner = auth()->user()->owner->owner_unique_id;

        $drivers = Driver::where('owner_id', $owner)->orderBy('created_at', 'asc')->get();
        $driverIds = $drivers->pluck('user_id')->toArray();

        $chat = ChatMessage::where('chat_id',$request->chat_id)
                ->whereIn('sender_id', $driverIds)
                ->where('seen_count', 0)
                ->update(['seen_count'=> 1]);

        // $chat = ChatMessage::where('chat_id',$request->chat_id)
        //         ->whereIn('sender_id', $driverIds)
        //         ->where('seen_count', 0)
        //         ->get();
        return $this->respondSuccess($chat);
    }

    public function delete(Order $chat)
    {
        $chat->delete();

        $message = trans('succes_messages.order_deleted_succesfully');
        return redirect('order')->with('success', $message);
    }
}
