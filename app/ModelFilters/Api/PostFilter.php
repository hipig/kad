<?php

namespace App\ModelFilters\Api;

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
        $query = $this->whereNotNull('published_at');
        switch ($type) {
            case 'recommend':
                return $query;
            case 'following':
                $followingIds = $user->following()->pluck('id');
                return $query->whereIn('user_id', $followingIds);
            case 'like':
                $likeIds = $user->postLikes()->pluck('post_id');
                return $query->whereIn('id', $likeIds);
            case 'collect':
                $collectIds = $user->postCollects()->pluck('post_id');
                return $query->whereIn('id', $collectIds);
        }

        return $query->latest('published_at');
    }
}
