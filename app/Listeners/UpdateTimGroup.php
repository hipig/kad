<?php

namespace App\Listeners;

use App\Events\ChatGroupCreated;
use App\Events\ChatGroupUpdated;
use App\Http\Integrations\TencentIM\Requests\Group\GroupCreateRequest;
use App\Http\Integrations\TencentIM\Requests\Group\GroupUpdateRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use App\Models\ChatGroup;
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

            $data = [];
            foreach (ChatGroup::$associatedFieldMap as $field => $tag) {
                if ($group->$field) {
                    $data[$tag] = $group->$field;
                }
            }

            $connector->send(new GroupUpdateRequest($group->group_key, $data));
        }
    }
}
