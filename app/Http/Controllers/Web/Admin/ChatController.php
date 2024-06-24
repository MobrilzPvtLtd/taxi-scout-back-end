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
use App\Models\Request\Chat;
use App\Base\Constants\Auth\Role;
use App\Jobs\Notifications\SendPushNotification;
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

        $messages = Chat::where('from_type', 4)->orderBy('created_at', 'asc')->get();
        $query = $this->chat->where('from_type', 4)
                ->join('users', 'chats.user_id', '=', 'users.id')
                ->select('chats.*', 'users.name', 'users.profile_picture', DB::raw('MAX(user_id) as max_id'))
                ->groupBy('user_id')
                ->orderBy('chats.created_at', 'desc');
                // ->get();

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();
        return view('admin.chat.index', compact('page', 'main_menu', 'sub_menu','messages','results'));

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

    public function getById($user_id)
    {
        $page = trans('pages_names.chat');
        $main_menu = 'manage-chat';
        $sub_menu = '';
        $auth_user = auth()->user()->id;
        // $messages = Chat::whereIn('from_type', [3,4])->orderBy('created_at', 'asc')->get();

        $message_info = $this->chat->where(['user_id'=> $user_id])->get();

        $subquery = DB::table('chats')->select(DB::raw('MAX(id) as max_id'))->groupBy('from_type');

        $user_messages = $this->chat->select('chats.*', 'users.name', 'users.profile_picture')
                            ->join('users', 'chats.user_id', '=', 'users.id')
                            ->where('from_type', '4')
                            ->whereIn('chats.id', $subquery)
                            ->orderBy('chats.created_at', 'desc')
                            ->get();
        // dd($user_messages);
        // $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.chat.create', compact('user_messages','message_info', 'page', 'main_menu', 'sub_menu'));
    }

    public function getConversations(Request $request)
    {
        // $conversations = Chat::whereIn(['user_id'=> $request->user_id, 'receiver_id'=> $request->user_id])->whereIn('from_type', [3,4])->orderBy('created_at', 'asc')->get();
        $conversations = Chat::where(function($query) use ($request) {
            $query->where('user_id', $request->user_id)
                  ->orWhere('receiver_id', $request->user_id);
        })
        ->whereIn('from_type', [3, 4])
        ->orderBy('created_at', 'asc')
        ->get();
        return $conversations;
    }

    public function store(Request $request)
    {
        $from_type = 3;

        $chatRequest = Chat::create([
            'message' => $request->message,
            'from_type' => $from_type,
            'user_id' => auth()->user()->id,
            'receiver_id' => $request->user_id
        ]);

        $driverDetail = User::find($chatRequest->user_id);

        $chats = Chat::whereIn('from_type', [3,4])->orderBy('created_at', 'asc')->get();

        $driver = $driverDetail;
        $notifable_driver = $driver->user;

        foreach ($chats as $key => $chat) {
            if ($chat->from_type == $from_type) {
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
        return $this->respondSuccess([null => 'message_sent_successfully', 'data' => $chatRequest]);
    }

    public function updateSeen(Request $request){
        Chat::where('user_id',$request->user_id)->where('from_type',4)->where('seen',0)->update(['seen'=>true]);
        return $this->respondSuccess(null, 'message_seen_successfully');
    }

    public function delete(Order $chat)
    {
        $chat->delete();

        $message = trans('succes_messages.order_deleted_succesfully');
        return redirect('order')->with('success', $message);
    }
}
