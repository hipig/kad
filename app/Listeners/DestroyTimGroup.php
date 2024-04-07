<?php

namespace App\Listeners;

use App\Events\ChatGroupCreated;
use App\Events\ChatGroupDissolved;
use App\Http\Integrations\TencentIM\Requests\Group\GroupCreateRequest;
use App\Http\Integrations\TencentIM\Requests\Group\GroupDestroyRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DestroyTimGroup
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
    public function handle(ChatGroupDissolved $event): void
    {
        $group = $event->getChatGroup();

        $connector = new TencentIMConnector();

        $response = $connector->send(new GroupDestroyRequest($group->group_key));

    }
}
