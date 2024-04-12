<?php

namespace App\ModelFilters\Admin;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\Auth;

class ChatGroupUserFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function group($groupId)
    {
        return $this->where('group_id', $groupId);
    }

    public function userIds($userIds)
    {
        return $this->whereIn('user_id', $userIds);
    }
}
