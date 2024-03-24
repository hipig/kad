<?php

namespace App\Models;


class TimEvent extends Model
{
    protected $fillable = [
        'type',
        'data'
    ];

    protected $casts = [
        'data' => 'json'
    ];
}
