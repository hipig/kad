<?php

namespace App\Models;


use DateTimeInterface;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
