<?php

namespace App\Models;

use Illuminate\Support\Str;

class ChatGroupMessage extends Model
{
    const STATUS_NORMAL = 1;
    const STATUS_RECALL= 2;

    protected $fillable = [
        'body',
        'random',
        'msg_seq',
        'online_only_flag'
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

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
