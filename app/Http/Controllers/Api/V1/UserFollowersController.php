<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserFollowerResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserFollower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFollowersController extends Controller
{
    public function following(Request $request, User $user)
    {
        $following = $user->following()->paginate($request->page_size ?? 15);

        $follows = UserFollower::query()->where('user_id', $user->id)->whereIn('follower_id', $following->getCollection()->pluck('id'))->get();
        foreach ($following->getCollection() as $item) {
            $item->is_followed = !!$follows->where('follower_id', $item->id)->first();
        }

        return UserResource::collection($following);
    }

    public function followers(Request $request, User $user)
    {
        $followers = $user->followers()->paginate($request->page_size ?? 15);

        $follows = UserFollower::query()->where('follower_id', $user->id)->whereIn('user_id', $followers->getCollection()->pluck('id'))->get();
        foreach ($followers->getCollection() as $item) {
            $item->is_followed = !!$follows->where('user_id', $item->id)->first();
        }

        return UserResource::collection($followers);
    }

    public function follow(Request $request, User $user)
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            throw new InvalidRequestException('不能关注自己哦。');
        }

        $follower = UserFollower::query()->where('user_id', $user->id)->where('follower_id', $authUser->id)->first();

        if (!$follower) {
            $follower = new UserFollower();
            $follower->user()->associate($user);
            $follower->follower()->associate($authUser);
            $follower->save();

            $user->increment('follower_count');
            $authUser->increment('following_count');
        }

        return UserFollowerResource::make($follower);
    }

    public function unFollow(Request $request, User $user)
    {
        $authUser = Auth::user();
        $follower = UserFollower::query()->where('user_id', $user->id)->where('follower_id', $authUser->id)->first();
        if ($follower) {
            $follower->delete();

            $user->decrement('follower_count');
            $authUser->decrement('following_count');
        }

        return response()->noContent();
    }
}
