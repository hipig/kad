<?php

namespace App\Listeners;

use App\Events\ChatGroupDissolved;
use App\Http\Integrations\TencentIM\Requests\Group\GroupDestroyRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DestroyTimGroup implements ShouldQueue
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

        if ($group->group_key) {
            $connector = new TencentIMConnector();

            $connector->send(new GroupDestroyRequest($group->group_key));
        }
    }
}
