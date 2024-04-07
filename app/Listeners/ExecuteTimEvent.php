<?php

namespace App\Listeners;

use App\Events\TimEventCreated;
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

        $methodMap = [
            TimEvent::TYPE_PROFILE_SET => 'setProfile',
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
