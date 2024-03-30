<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostLikePolicy
{
    public function own(User $user, PostLike $like): bool
    {
        return $user->id === $like->user_id;
    }
}
