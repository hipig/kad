<?php

namespace App\Listeners;

use App\Events\ChatGroupMessageRecalled;
use App\Events\ChatMessageWithdrew;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Http\Integrations\TencentIM\Requests\Group\GroupMessageRecallRequest;
use App\Http\Integrations\TencentIM\Requests\Message\MessageWithdrawRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WithdrawTimMessage implements ShouldQueue
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
    public function handle(ChatMessageWithdrew $event): void
    {
        $message = $event->getChatMessage();

        $connector = new TencentIMConnector();

        $connector->send(new MessageWithdrawRequest($message->user->username, $message->toUser->username, $message->msg_key));
    }
}
