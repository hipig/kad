<?php

namespace App\Policies;

use App\Models\PostBlock;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostBlockPolicy
{
    public function own(User $user, PostBlock $block): bool
    {
        return $user->id === $block->user_id;
    }
}
