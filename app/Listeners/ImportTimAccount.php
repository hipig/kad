<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Http\Integrations\TencentIM\Requests\OpenLogin\AccountImportRequest;
use App\Http\Integrations\TencentIM\Requests\Profile\PortraitSetRequest;
use App\Http\Integrations\TencentIM\TencentIMConnector;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ImportTimAccount implements ShouldQueue
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
    public function handle(UserCreated $event): void
    {
        $user = $event->getUser();

        $connector = new TencentIMConnector();
        $connector->send(new AccountImportRequest($user->username, $user->nickname, $user->avatar ?? ''));
    }
}
