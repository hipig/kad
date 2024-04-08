<?php

namespace App\Listeners;

use App\Events\ChatGroupMessageSent;
use App\Events\ChatMessageSent;
use App\Http\Integrations\TencentIM\Requests\Group\GroupMessageSendRequest;
use App\Http\Integrations\TencentIM\Requests\Message\MessageSendRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTimMessage implements ShouldQueue
{
    public $afterCommit = true;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ChatMessageSent $event): void
    {
        $message = $event->getChatMessage();

        $connector = new TencentIMConnector();

        $response = $connector->send(new MessageSendRequest($message->user->username, $message->toUser->username, $message->body, $message->random,));

        $message->msg_seq = $response->json('MsgSeq');
        $message->msg_key = $response->json('MsgKey');
        $message->sent_at = now();
        $message->save();
    }
}
