<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Http\Integrations\TencentIM\Requests\OpenLogin\AccountImportRequest;
use App\Http\Integrations\TencentIM\Requests\Profile\PortraitSetRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetTimPortrait implements ShouldQueue
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
    public function handle(UserUpdated $event): void
    {
        $user = $event->getUser();

        $connector = new TencentIMConnector();

        $data = [];
        foreach (User::$associatedFieldMap as $field => $tag) {
            if ($user->$field) {
                $data[] = [
                    'Tag' => $tag,
                    'Value' => $user->$field
                ];
            }
        }

        $connector->send(new PortraitSetRequest($user->username, $data));
    }
}
