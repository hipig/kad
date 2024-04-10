<?php

namespace App\Models;


class UserOnlineRecord extends Model
{
    protected $fillable = [
        'action',
        'reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
