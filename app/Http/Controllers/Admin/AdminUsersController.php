<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Http\Resources\AdminUserResource;
use App\ModelFilters\Admin\AdminUserFilter;
use App\Models\AdminUser;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
        $users = AdminUser::filter($request->all(), AdminUserFilter::class)->latest()->paginate($request->page_size ?? 15);

        return AdminUserResource::collection($users);
    }

    public function store(AdminUserRequest $request)
    {
        $user = new AdminUser(
            $request->only([
                'name', 'username'
            ])
        );
        $user->password = $request->password;
        $user->save();

        return AdminUserResource::make($user);
    }

    public function update(AdminUserRequest $request, AdminUser $user)
    {
        $user->fill(
            $request->only([
                'name', 'username'
            ])
        );
        $user->save();

        return AdminUserResource::make($user);
    }
}
