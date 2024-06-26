<?php

namespace App\ModelFilters\Admin;

use EloquentFilter\ModelFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ChatGroupFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function name($name)
    {
        return $this->where('name', 'like', "%{$name}%");
    }

    public function owner($ownerId)
    {
        return $this->where('owner_id', $ownerId);
    }

    public function userIds($userIds)
    {
        return $this->whereHas('users', function (Builder $q) use ($userIds) {
            $q->whereIn('user_id', $userIds);
        });
    }

}
