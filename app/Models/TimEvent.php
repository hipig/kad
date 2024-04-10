<?php

namespace App\Models;


class TimEvent extends Model
{
    const TYPE_GROUP_CREATED = 'Group.CallbackAfterCreateGroup'; // 创建群组后
    const TYPE_GROUP_DESTROYED = 'Group.CallbackAfterGroupDestroyed'; // 群组解散后
    const TYPE_GROUP_USER_EXITED = 'Group.CallbackAfterMemberExit'; // 群组成员离开后
    const TYPE_GROUP_USER_JOINED = 'Group.CallbackAfterNewMemberJoin'; // 群组成员加入后
    const TYPE_GROUP_MESSAGE_SENT = 'Group.CallbackAfterSendMsg'; // 群组发言后
    const TYPE_GROUP_MESSAGE_RECALL = 'Group.CallbackAfterRecallMsg'; // 群组发言撤回后
    const TYPE_GROUP_INFO_CHANGED = 'Group.CallbackAfterGroupInfoChanged'; // 群组资料变动后

    const TYPE_FRIEND_ADD = 'Sns.CallbackFriendAdd'; // 加好友后
    const TYPE_FRIEND_DELETE = 'Sns.CallbackFriendDelete'; // 删除好友后
    const TYPE_BLACKLIST_ADD = 'Sns.CallbackBlackListAdd'; // 添加黑名单后
    const TYPE_BLACKLIST_DELETE = 'Sns.CallbackBlackListDelete'; // 删除黑名单后

    const TYPE_PROFILE_SET = 'Profile.CallbackPortraitSet'; // 用户资料设置后

    const TYPE_MESSAGE_SENT = 'C2C.CallbackAfterSendMsg'; // 单聊消息发送后
    const TYPE_MESSAGE_WITHDRAW = 'C2C.CallbackAfterMsgWithDraw'; // 单聊消息撤回后
    const TYPE_STATE_CHANGE = 'State.StateChange'; // 用户状态变更

    const PLATFORM_RESTAPI = 'RESTAPI';
    const PLATFORM_ANDROID = 'Android';


    protected $fillable = [
        'type',
        'platform',
        'client_ip',
        'data'
    ];

    protected $casts = [
        'data' => 'json',
        'executed_at' => 'datetime'
    ];
}
