<?php

namespace App\Policies;

use App\Models\PostCommentLike;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostCommentLikePolicy
{
    public function own(User $user, PostCommentLike $like): bool
    {
        return $user->id === $like->user_id;
    }
}
