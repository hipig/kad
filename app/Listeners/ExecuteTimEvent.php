<?php

namespace App\Listeners;

use App\Events\TimEventCreated;
use App\Models\ChatGroup;
use App\Models\ChatGroupUser;
use App\Models\TimEvent;
use App\Models\User;
use App\Models\UserFriend;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ExecuteTimEvent
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TimEventCreated $event): void
    {
        $timEvent = $event->getEvent();
        $data = $timEvent->data;

        if ($timEvent->platform === TimEvent::PLATFORM_RESTAPI) {
            return;
        }

        $methodMap = [
            TimEvent::TYPE_PROFILE_SET => 'setProfile',
            TimEvent::TYPE_GROUP_CREATED => 'createGroup',
            TimEvent::TYPE_GROUP_DESTROYED => 'destroyGroup',
            TimEvent::TYPE_FRIEND_ADD => 'addFriend',
            TimEvent::TYPE_FRIEND_DELETE => 'deleteFriend',
        ];

        $method = $methodMap[$timEvent->type] ?? '';

        if ($method && method_exists($this, $method)) {
            $this->$method($data);

            $timEvent->executed_at = now();
            $timEvent->save();
        }
    }

    protected function setProfile($data)
    {
        $updateData = [];
        $fieldMap = array_flip(User::$associatedFieldMap);
        $fromUser = User::query()->where('username', $data['From_Account'])->first();
        foreach ($data['ProfileItem'] as $datum) {
            $field = $fieldMap[$datum['Tag']] ?? '';
            if ($field) {
                $updateData[$field] = $datum['Value'];
            }
        }
        $fromUser->forceFill($updateData);
        $fromUser->save();
    }

    public function createGroup($data)
    {
        DB::transaction(function () use ($data) {

            $group = ChatGroup::create([
                'name' => $data['Name'],
                'type' => $data['Type']
            ]);

            if ($data['Owner_Account']) {
                $owner = User::query()->where('username', $data['Owner_Account'])->first();
                $group->increment('member_num');
                $group->owner()->associate($owner);
                $group->group_key = $data['GroupId'];
                $group->save();

                $groupUser = new ChatGroupUser();
                $groupUser->user()->associate($owner);
                $groupUser->role = ChatGroupUser::ROLE_OWNER;
                $groupUser->join_at = now();
                $groupUser->save();
            }
        });
    }

    public function destroyGroup($data)
    {
        $group = ChatGroup::query()->where('group_key', $data['GroupId'])->first();
        if ($group) {
            $group->status = ChatGroup::STATUS_DISSOLVE;
            $group->save();
        }
    }

    protected function addFriend($data)
    {
        DB::transaction(function () use ($data) {
            foreach ($data['PairList'] as $datum) {
                $users = User::query()->whereIn('username', [$datum['To_Account'], $datum['From_Account'], $datum['Initiator_Account']])->get();
                $toUser = $users->where('username', $datum['To_Account'])->first();
                $fromUser = $users->where('username', $datum['From_Account'])->first();
                $initiatorUser = $users->where('username', $datum['Initiator_Account'])->first();

                $friend = new UserFriend();
                $friend->user()->associate($toUser);
                $friend->friend()->associate($fromUser);
                $friend->initiator()->associate($initiatorUser);
                $friend->save();

                $fromFriend = new UserFriend();
                $fromFriend->user()->associate($fromUser);
                $fromFriend->friend()->associate($toUser);
                $fromFriend->initiator()->associate($initiatorUser);
                $fromFriend->save();

                $toUser->increment('friend_count');
                $fromUser->increment('friend_count');
            }
        });

    }

    protected function deleteFriend($data)
    {
        DB::transaction(function () use ($data) {
            foreach ($data['PairList'] as $datum) {
                $users = User::query()->whereIn('username', [$datum['To_Account'], $datum['From_Account']])->get();
                $toUser = $users->where('username', $datum['To_Account'])->first();
                $fromUser = $users->where('username', $datum['From_Account'])->first();

                UserFriend::query()->whereIn('user_id', [$toUser->id, $fromUser->id])->update([
                    'status' => UserFriend::STATUS_DELETED
                ]);
                $toUser->decrement('friend_count');
                $fromUser->decrement('friend_count');
            }
        });

    }
}
