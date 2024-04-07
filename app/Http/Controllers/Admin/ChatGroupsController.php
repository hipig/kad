<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatGroupCreated;
use App\Events\ChatGroupDissolved;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatGroupRequest;
use App\Http\Resources\ChatGroupResource;
use App\ModelFilters\Admin\ChatGroupFilter;
use App\Models\ChatGroup;
use App\Models\ChatGroupUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatGroupsController extends Controller
{
    public function index(Request $request)
    {
        $groups = ChatGroup::filter($request->all(), ChatGroupFilter::class)->latest()->paginate($request->page_size ?? 15);

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
}
