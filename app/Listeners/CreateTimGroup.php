<?php

namespace App\Listeners;

use App\Events\ChatGroupCreated;
use App\Http\Integrations\TencentIM\Requests\Group\GroupCreateRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateTimGroup
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
    public function handle(ChatGroupCreated $event): void
    {
        $group = $event->getChatGroup();

        $connector = new TencentIMConnector();

        $response = $connector->send(new GroupCreateRequest($group->name, $group->type, $group->owner?->username));

        if ($response->json('ErrorCode') === 0) {
            $group->group_key = $response->json('GroupId');
            $group->save();
        }
    }
}
