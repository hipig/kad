<?php

namespace App\Models;

class ChatMessage extends Model
{
    const STATUS_NORMAL = 1;
    const STATUS_WITHDRAW= 2;

    protected $fillable = [
        'body'
    ];

    protected $casts = [
        'body' => 'array',
        'sent_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->random) {
                $model->random = random_int(0, 999999);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
