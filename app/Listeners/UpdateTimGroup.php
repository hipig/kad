<?php

namespace App\Listeners;

use App\Events\ChatGroupCreated;
use App\Events\ChatGroupUpdated;
use App\Http\Integrations\TencentIM\Requests\Group\GroupCreateRequest;
use App\Http\Integrations\TencentIM\Requests\Group\GroupUpdateRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateTimGroup implements ShouldQueue
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
    public function handle(ChatGroupUpdated $event): void
    {
        $group = $event->getChatGroup();

        if ($group->group_key) {
            $connector = new TencentIMConnector();

            $connector->send(new GroupUpdateRequest($group->group_key, $group->name, $group->introduction ?? '', $group->notification ?? ''));
        }
    }
}
