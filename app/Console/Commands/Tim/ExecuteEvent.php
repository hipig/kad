<?php

namespace App\Console\Commands\Tim;

use App\Events\TimEventCreated;
use App\Models\TimEvent;
use Illuminate\Console\Command;

class ExecuteEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tim:execute-event {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tim execute event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $eventId = $this->argument('id');

        $event = TimEvent::query()->where('id', $eventId)->first();
        if ($event) {
            event(new TimEventCreated($event));
        }
    }
}
