<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Http\Integrations\TencentIM\Requests\OpenLogin\AccountImportRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ImportTencentImAccount
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
    public function handle(UserCreated $event): void
    {
        $user = $event->getUser();

        $connector = new TencentIMConnector();
        $connector->send(new AccountImportRequest($user->username, $user->nickname, $user->avatar ?? ''));
    }
}
