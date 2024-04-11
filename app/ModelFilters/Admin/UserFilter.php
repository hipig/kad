<?php

namespace App\ModelFilters\Admin;

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

    public function keyword($keyword)
    {
        return $this->where('nickname', 'like', "%{$keyword}%")->orWhere('wallet_account', $keyword);
    }

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

    public function status($status)
    {
        return $this->where('status', $status);
    }
}
