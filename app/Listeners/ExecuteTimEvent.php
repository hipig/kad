<?php

namespace App\Listeners;

use App\Events\TimEventCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ExecuteTimEvent
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
    public function handle(TimEventCreated $event): void
    {
        $timEvent = $event->getEvent();
    }
}
