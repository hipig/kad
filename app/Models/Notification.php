<?php

namespace App\Models;


use DateTimeInterface;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
