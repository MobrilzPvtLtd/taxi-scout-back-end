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

    public function index()
    {

        $page = trans('pages_names.chat');

        $main_menu = 'manage-chat';
        $sub_menu = '';

        $messages = Chat::orderBy('created_at', 'asc')->get();
        return view('admin.chat.index', compact('page', 'main_menu', 'sub_menu','messages'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        // if(!access()->hasRole(RoleSlug::SUPER_ADMIN)){
        //     $query = $query->where('orders.user_id', auth()->user()->id);
        // }

        $subquery = DB::table('chats')->select(DB::raw('MAX(id) as max_id'))->groupBy('user_id');

        $query = $this->chat->select('chats.*', 'users.name', 'users.profile_picture')
                            ->join('users', 'chats.user_id', '=', 'users.id')
                            ->where('from_type', 4)
                            ->whereIn('chats.id', $subquery)
                            ->orderBy('chats.created_at', 'desc');

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.chat._chat', compact('results'));
    }

    public function getById($user_id)
    {
        $page = trans('pages_names.chat');
        $main_menu = 'manage-chat';
        $sub_menu = '';
        $messages = Chat::orderBy('created_at', 'asc')->get();

        return view('admin.chat.create', compact('messages', 'page', 'main_menu', 'sub_menu'));
    }

    public function store(Request $request)
    {
        if (access()->hasRole(Role::ADMIN)) {
            $from_type = 3;
        } else {
            $from_type = 4;
        }
        $request_detail = RequestModel::find($request->request_id);

        $request_detail = Chat::create([
            'message' => $request->message,
            'from_type' => $from_type,
            'user_id' => auth()->user()->id
        ]);

        $driverDetail = User::find($request_detail->user_id);

        $chats = Chat::orderBy('created_at', 'asc')->get();
        if (access()->hasRole(Role::ADMIN)) {
            $from_type = 3;
            $user_type = 'company';
            $driver = $driverDetail;
            dd($driver);
            $notifable_driver = $driver->user;
        } else {
            $from_type = 4;
            $user_type = 'driver';
            $driver = $request_detail->userDetail;
            $notifable_driver = $driver;
        }
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

        return $this->respondSuccess(null, 'message_sent_successfully');
    }

    public function delete(Order $chat)
    {
        $chat->delete();

        $message = trans('succes_messages.order_deleted_succesfully');
        return redirect('order')->with('success', $message);
    }
}
