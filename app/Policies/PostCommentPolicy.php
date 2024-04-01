<?php

namespace App\Policies;

use App\Models\PostComment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostCommentPolicy
{
    public function own(User $user, PostComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }
}
