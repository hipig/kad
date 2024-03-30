<?php

namespace App\Models;


class TimEvent extends Model
{
    protected $fillable = [
        'type',
        'platform',
        'client_ip',
        'data'
    ];

    protected $casts = [
        'data' => 'json'
    ];
}
