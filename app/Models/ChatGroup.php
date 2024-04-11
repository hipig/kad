<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;

class ChatGroup extends Model
{
    const TYPE_PRIVATE = 'Private';
    const TYPE_PUBLIC= 'Public';
    const TYPE_CHAT_ROOM = 'ChatRoom';
    const TYPE_AV_CHAT_ROOM = 'AVChatRoom';
    const TYPE_COMMUNITY = 'Community';

    public static $typeMap = [
        self::TYPE_PRIVATE => 'Work 好友工作群',
        self::TYPE_PUBLIC => 'Public 陌生人社交群',
        self::TYPE_CHAT_ROOM => 'Meeting 临时会议群',
        self::TYPE_AV_CHAT_ROOM => 'AVChatRoom 直播群',
        self::TYPE_COMMUNITY => 'Community 社群'
    ];

    const STATUS_NORMAL = 1;
    const STATUS_DISSOLVE = 2;

    public static $associatedFieldMap = [
        'name' => 'Name',
        'avatar' => 'FaceUrl',
        'introduction' => 'Introduction',
        'notification' => 'Notification',
        'max_member_num' => 'MaxMemberNum',
        'mute_all_member' => 'MuteAllMember',
    ];

    protected $fillable = [
        'name',
        'avatar',
        'type',
        'owner_id',
        'group_key',
        'introduction',
        'notification',
        'max_member_num',
        'apply_join_option',
        'invite_join_option',
        'mute_all_member',
    ];

    protected $casts = [
        'last_info_at' => 'datetime',
        'last_msg_at' => 'datetime'
    ];

    protected $appends = [
        'type_text'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->hasMany(ChatGroupUser::class, 'group_id');
    }

    protected function typeText(): Attribute
    {
        return Attribute::get(function () {
            return self::$typeMap[$this->type];
        });
    }
}
