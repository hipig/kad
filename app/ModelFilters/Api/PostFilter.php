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
        $query = $this->where('visible_status', Post::VISIBLE_STATUS_COMMON);
        switch ($type) {
            case 'recommend':
                $query->inRandomOrder()->where('user_id', '<>', $user->id);
                break;
            case 'following':
                $followingIds = UserFollower::query()->where('follower_id', $user->id)->pluck('user_id');
                $query->whereIn('user_id', $followingIds);
                break;
            case 'like':
                $likePostIds = $user->postLikes()->pluck('post_id');
                $query->whereIn('id', $likePostIds);
                break;
            case 'collect':
                $collectPostIds = $user->postCollects()->pluck('post_id');
                $query->whereIn('id', $collectPostIds);
                break;
            case 'comment':
                $commentPostIds = PostComment::query()->where('user_id', $user->id)->pluck('post_id');
                $query->whereIn('id', $commentPostIds);
                break;
            case 'myself':
                $query = $this->where('user_id', $user->id)->latest('top_at');
                break;
        }

        return $query;
    }
}
