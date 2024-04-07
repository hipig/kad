<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatGroupUserResource;
use App\ModelFilters\Admin\ChatGroupUserFilter;
use App\Models\ChatGroupUser;
use Illuminate\Http\Request;

class ChatGroupUsersController extends Controller
{
    public function index(Request $request)
    {
        $users = ChatGroupUser::filter($request->all(), ChatGroupUserFilter::class)->with(['group', 'user'])->latest()->paginate($request->page_size ?? 15);

        return ChatGroupUserResource::collection($users);
    }
}
