<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Models\Request\Chat;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Request\Request as RequestModel;
use App\Base\Constants\Masters\PushEnums;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Jobs\NotifyViaMqtt;
use Illuminate\Http\Request;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * @group Request-Chat
 *
 * APIs for In app chat b/w user/driver
 */
class ChatController extends BaseController
{

    protected $chat;

    function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }


    /**
     * Chat history for both user & driver
     *
     */
    public function history(Request $request)
    {
        $conversations = Chat::where(function($query) {
            $query->where('user_id', auth()->user()->id)
                  ->orWhere('receiver_id', auth()->user()->id);
        })
        ->whereIn('from_type', [3, 4])
        ->orderBy('created_at', 'asc')
        ->get();

        return $this->respondSuccess($conversations, 'chats_listed');
    }

    public function updateSeen(Request $request){
        Chat::where(function($query) {
            $query->where('user_id', auth()->user()->id)
                  ->orWhere('receiver_id',  auth()->user()->id);
        })->where('from_type', 3)->update(['seen' => true]);

        return $this->respondSuccess(null, 'message_seen_successfully');


    }

    public function send(Request $request)
    {
        $company = User::where('company_key', auth()->user()->driver->company_key)->first();
        $from_type = 4;

        $chatRequest = Chat::create([
            'message' => $request->message,
            'from_type' => $from_type,
            'user_id' => auth()->user()->id,
            'receiver_id' => $company->id
        ]);

        $driverDetail = User::find($chatRequest->user_id);

        $chats = Chat::whereIn('from_type', [3, 4])->orderBy('created_at', 'asc')->get();

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

        return $this->respondSuccess(null, 'message_sent_successfully');
    }
}
