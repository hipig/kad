<?php

namespace App\ModelFilters\Api;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\UserFollower;
use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\Auth;

class PostFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function type($type)
    {
        $user = Auth::user();
        $query = $this;
        switch ($type) {
            case 'recommend':
                break;
            case 'following':
                $followingIds = UserFollower::query()->where('follower_id', $user->id)->pluck('user_id');
                $query->whereIn('user_id', $followingIds)->where('visible_status', Post::VISIBLE_STATUS_COMMON);
                break;
            case 'like':
                $likePostIds = $user->postLikes()->pluck('post_id');
                $query->whereIn('id', $likePostIds)->where('visible_status', Post::VISIBLE_STATUS_COMMON);
                break;
            case 'collect':
                $collectPostIds = $user->postCollects()->pluck('post_id');
                $query->whereIn('id', $collectPostIds)->where('visible_status', Post::VISIBLE_STATUS_COMMON);
                break;
            case 'comment':
                $commentPostIds = PostComment::query()->where('user_id', $user->id)->pluck('post_id');
                $query->whereIn('id', $commentPostIds)->where('visible_status', Post::VISIBLE_STATUS_COMMON);
                break;
            case 'myself':
                $query->where('user_id', $user->id)->latest('top_at');
                break;
        }

        return $query;
    }
}
