<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatGroupCreated;
use App\Events\ChatGroupDissolved;
use App\Events\ChatGroupExited;
use App\Events\ChatGroupJoined;
use App\Events\ChatGroupMessageSent;
use App\Events\ChatGroupUpdated;
use App\Exceptions\InvalidRequestException;
use App\Exports\ChatGroupExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatGroupRequest;
use App\Http\Resources\ChatGroupResource;
use App\ModelFilters\Admin\ChatGroupFilter;
use App\Models\ChatGroup;
use App\Models\ChatGroupMessage;
use App\Models\ChatGroupUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatGroupsController extends Controller
{
    public function index(Request $request)
    {
        $groups = ChatGroup::filter($request->all(), ChatGroupFilter::class)->with(['owner'])->latest()->paginate($request->page_size ?? 15);

        return ChatGroupResource::collection($groups);
    }

    public function store(ChatGroupRequest $request)
    {
        $group = DB::transaction(function () use ($request) {
            $group = ChatGroup::create(
                $request->only([
                    'name', 'owner_id', 'type'
                ])
            );

            if ($group->owner) {
                $group->increment('member_num');
                $group->owner->increment('chat_group_count');

                $groupUser = new ChatGroupUser();
                $groupUser->group()->associate($group);
                $groupUser->user()->associate($group->owner);
                $groupUser->role = ChatGroupUser::ROLE_OWNER;
                $groupUser->join_at = now();
                $groupUser->save();
            }

            event(new ChatGroupCreated($group));

            return $group;
        });

        return ChatGroupResource::make($group);
    }

    public function update(Request $request, ChatGroup $group)
    {
        if ($group->status === ChatGroup::STATUS_DISSOLVE) {
            throw new InvalidRequestException('该群组已解散，不能修改');
        }

        $group->fill(
            $request->only(['name', 'avatar', 'introduction', 'notification', 'max_member_num', 'apply_join_option', 'invite_join_option', 'mute_all_member'])
        );
        $group->last_info_at = now();
        $group->save();

        event(new ChatGroupUpdated($group));

        return ChatGroupResource::make($group);
    }

    public function show(Request $request, ChatGroup $group)
    {
        $group->load(['owner']);
        return ChatGroupResource::make($group);
    }

    public function dissolve(Request $request)
    {
        $groups = ChatGroup::query()->whereIn('id', $request->group_ids)->get();
        foreach ($groups as $group) {
            $group->status = ChatGroup::STATUS_DISSOLVE;
            $group->save();

            event(new ChatGroupDissolved($group));
        }

        return ChatGroupResource::collection($groups);
    }

    public function join(Request $request, ChatGroup $group)
    {
        if ($group->status === ChatGroup::STATUS_DISSOLVE) {
            throw new InvalidRequestException('该群组已解散，不能添加群成员');
        }

        $group = DB::transaction(function () use ($request, $group) {
            $silence = $request->silence ?? 0;
            $userIds = $request->user_ids;
            $joinedUserIds = ChatGroupUser::query()->whereIn('user_id', $userIds)->where('status', ChatGroupUser::STATUS_NORMAL)->pluck('user_id')->toArray();
            $users = User::query()->whereIn('id', array_diff($userIds, $joinedUserIds))->get();

            foreach ($users as $user) {
                $group->increment('member_num');
                $user->increment('chat_group_count');

                $groupUser = new ChatGroupUser();
                $groupUser->group()->associate($group);
                $groupUser->user()->associate($user);
                $groupUser->role = ChatGroupUser::TYPE_MEMBER;
                $groupUser->join_at = now();
                $groupUser->save();
            }
            $usernameList = $users->pluck('username')->toArray();
            event(new ChatGroupJoined($group, $usernameList, $silence));

            return $group;
        });

        return ChatGroupResource::make($group);
    }

    public function exit(Request $request, ChatGroup $group)
    {
        if ($group->status === ChatGroup::STATUS_DISSOLVE) {
            throw new InvalidRequestException('该群组已解散，不能删除群成员');
        }

        $group = DB::transaction(function () use ($request, $group) {
            $silence = $request->silence ?? 0;
            $groupUserIds = $request->group_user_ids;
            $userIds = ChatGroupUser::query()->whereIn('id', $groupUserIds)->pluck('user_id');
            $usernameList = User::query()->whereIn('id', $userIds)->pluck('username')->toArray();

            $group->increment('member_num', count($userIds));
            User::query()->whereIn('id', $userIds)->decrement('chat_group_count');
            ChatGroupUser::query()->whereIn('id', $groupUserIds)->update([
                'status' => ChatGroupUser::STATUS_EXIT
            ]);

            event(new ChatGroupExited($group, $usernameList, $silence));

            return $group;
        });

        return ChatGroupResource::make($group);
    }

    public function export(Request $request)
    {
        return (new ChatGroupExport($request->all()))->download('群组列表.xlsx');
    }
}
