<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\ModelFilters\Admin\UserFilter;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::filter($request->all(), UserFilter::class)->latest()->paginate($request->page_size ?? 15);

        return UserResource::collection($users);
    }
}