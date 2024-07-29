<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Models\Chat;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Request\Request as RequestModel;
use App\Base\Constants\Masters\PushEnums;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Jobs\NotifyViaMqtt;
use Illuminate\Http\Request;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Admin\Driver;
use App\Models\ChatMessage;
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
        $chatAuth = Chat::where('user_id', auth()->user()->id)->first();
        $conversations = ChatMessage::where(function($query) {
            $query->where('sender_id', auth()->user()->id)
                  ->orWhere('receiver_id', auth()->user()->id);
        })
        ->where('chat_id', $chatAuth->id)
        ->orderBy('created_at', 'asc')
        ->get();

        return $this->respondSuccess($conversations, 'chats_listed');
    }

    public function updateSeen(Request $request){
        $chatAuth = Chat::where('user_id', auth()->user()->id)->first();

        ChatMessage::where(function($query) {
            $query->where('receiver_id', auth()->user()->id);
                //   ->orWhere('receiver_id',  auth()->user()->id);
        })->where('chat_id', $chatAuth->id)->update(['seen_count' => true]);

        return $this->respondSuccess(null, 'message_seen_successfully');


    }

    public function send(Request $request)
    {
        // $company = Driver::where('owner_id', auth()->user()->driver->owner_id)->first();
        $chatAuth = Chat::where('user_id', auth()->user()->id)->first();
        $from_type = 4;


        if(!$chatAuth){
            $chatRequest = Chat::create(['user_id' => auth()->user()->id]);
        }

        $chatMessage = new ChatMessage();
        $chatMessage->chat_id = $chatAuth ? $chatAuth->id : $chatRequest->id;
        $chatMessage->message = $request->message;
        $chatMessage->sender_id = auth()->user()->id;
        $chatMessage->receiver_id = auth()->user()->driver->owner->user_id;
        $chatMessage->save();

        $driverDetail = User::find($chatMessage->sender_id);

        $chats = ChatMessage::where('chat_id', $chatMessage->chat_id)->orderBy('created_at', 'asc')->get();
        // $chats = Chat::whereIn('from_type', [3, 4])->orderBy('created_at', 'asc')->get();

        $driver = $driverDetail;
        $notifable_driver = $driver->user;

        foreach ($chats as $key => $chat) {
            if ($chat->receiver_id == auth()->user()->driver->owner->user_id) {
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
