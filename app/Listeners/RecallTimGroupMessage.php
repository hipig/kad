<?php

namespace App\Listeners;

use App\Events\ChatGroupMessageRecalled;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Http\Integrations\TencentIM\Requests\Group\GroupMessageRecallRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecallTimGroupMessage implements ShouldQueue
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
    public function handle(ChatGroupMessageRecalled $event): void
    {
        $message = $event->getChatGroupMessage();

        $connector = new TencentIMConnector();

        $seqList = [
            [
                'MsgSeq' => $message->msg_seq
            ]
        ];
        $connector->send(new GroupMessageRecallRequest($message->group->group_key, $seqList));
    }
}
