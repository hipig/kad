<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class ChatMessage extends Model
{
    const STATUS_NORMAL = 1;
    const STATUS_WITHDRAW= 2;

    public static $statusMap = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_WITHDRAW => '撤回'
    ];

    protected $fillable = [
        'body',
        'random',
        'msg_seq',
        'msg_key',
        'online_only_flag'
    ];

    protected $casts = [
        'body' => 'array',
        'sent_at' => 'datetime'
    ];

    protected $appends = [
        'status_text'
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

    protected function statusText(): Attribute
    {
        return Attribute::get(function () {
            return self::$statusMap[$this->status] ?? '';
        });
    }
}
