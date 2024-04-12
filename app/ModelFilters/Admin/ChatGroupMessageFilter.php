<?php

namespace App\ModelFilters\Admin;

use App\Models\ChatGroup;
use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\Auth;

class ChatGroupMessageFilter extends ModelFilter
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
        $groupIds = ChatGroup::query()->where('name', 'like', "%{$name}%")->pluck('id');
        return $this->whereIn('group_id', $groupIds);
    }

    public function userIds($userIds)
    {
        return $this->whereIn('user_id', $userIds);
    }

}
