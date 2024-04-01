<?php

namespace App\Policies;

use App\Models\PostCollect;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostCollectPolicy
{
    public function own(User $user, PostCollect $collect): bool
    {
        return $user->id === $collect->user_id;
    }
}
