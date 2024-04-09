<?php

namespace App\Listeners;

use App\Events\ChatGroupCreated;
use App\Events\ChatGroupJoined;
use App\Http\Integrations\TencentIM\Requests\Group\GroupAddMemberRequest;
use App\Http\Integrations\TencentIM\Requests\Group\GroupCreateRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class JoinTimGroup
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
    public function handle(ChatGroupJoined $event): void
    {
        $group = $event->getGroup();
        $usernameList = $event->getUsernameList();
        $silence = $event->getSilence();

        $connector = new TencentIMConnector();

        $accountList = array_map(function ($item) {
            return ['Member_Account' => $item];
        }, $usernameList);

        $response = $connector->send(new GroupAddMemberRequest($group->group_key, $accountList, $silence));

        if ($response->json('ErrorCode') === 0) {
            $group->group_key = $response->json('GroupId');
            $group->save();
        }
    }
}
