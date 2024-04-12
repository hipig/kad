<?php

namespace App\ModelFilters\Api;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\UserFollower;
use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\Auth;

class UserFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function nickname($nickname)
    {
        return $this->where('nickname', 'like', "%{$nickname}%");
    }

    public function walletAccount($walletAccount)
    {
        return $this->where('wallet_account', $walletAccount);
    }

    public function username($username)
    {
        return $this->where('username', $username);
    }
}
