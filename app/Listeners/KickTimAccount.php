<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserDisabled;
use App\Http\Integrations\TencentIM\Requests\OpenLogin\AccountImportRequest;
use App\Http\Integrations\TencentIM\Requests\OpenLogin\AccountKickRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class KickTimAccount implements ShouldQueue
{
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
    public function handle(UserDisabled $event): void
    {
        $user = $event->getUser();

        $connector = new TencentIMConnector();
        $connector->send(new AccountKickRequest($user->username));
    }
}
