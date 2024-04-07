<?php

namespace App\Models;


class UserFriend extends Model
{
    const STATUS_NORMAL = 1;
    const STATUS_DELETED = 2;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }
}
