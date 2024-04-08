<?php

namespace App\Listeners;

use App\Events\ChatGroupMessageSent;
use App\Http\Integrations\TencentIM\Requests\Group\GroupMessageSendRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTimGroupMessage implements ShouldQueue
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
    public function handle(ChatGroupMessageSent $event): void
    {
        $message = $event->getChatGroupMessage();

        $connector = new TencentIMConnector();

        $response = $connector->send(new GroupMessageSendRequest($message->group->group_key, $message->body, $message->random, $message->online_only_flag));

        if ($msgSeq = $response->json('MsgSeq')) {
            $message->msg_seq = $msgSeq;
            $message->sent_at = now();
            $message->save();
        }
    }
}
