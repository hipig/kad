<?php

namespace App\Listeners;

use App\Events\ChatGroupCreated;
use App\Events\ChatGroupExited;
use App\Events\ChatGroupJoined;
use App\Http\Integrations\TencentIM\Requests\Group\GroupAddMemberRequest;
use App\Http\Integrations\TencentIM\Requests\Group\GroupCreateRequest;
use App\Http\Integrations\TencentIM\Requests\Group\GroupDeleteMemberRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ExitTimGroup implements ShouldQueue
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
    public function handle(ChatGroupExited $event): void
    {
        $group = $event->getGroup();
        $usernameList = $event->getUsernameList();
        $silence = $event->getSilence();

        if ($group->group_key && $usernameList) {
            $connector = new TencentIMConnector();

            $connector->send(new GroupDeleteMemberRequest($group->group_key, $usernameList, $silence));
        }
    }
}
