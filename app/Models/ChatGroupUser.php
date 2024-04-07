<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;

class ChatGroupUser extends Model
{
    const ROLE_OWNER = 'Owner';
    const ROLE_ADMIN = 'Admin';
    const TYPE_MEMBER = 'Member';

    public static $roleMap = [
        self::ROLE_OWNER => '群主',
        self::ROLE_ADMIN => '管理员',
        self::TYPE_MEMBER => '成员',
    ];

    protected $fillable = [
        'user_id',
    ];

    protected $casts = [
        'join_at' => 'datetime',
        'last_send_msg_at' => 'datetime'
    ];

    protected $appends = [
        'role_text'
    ];

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function roleText(): Attribute
    {
        return Attribute::get(function () {
            return self::$roleMap[$this->role];
        });
    }
}
