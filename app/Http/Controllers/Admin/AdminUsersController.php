<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Resources\AdminUserResource;
use App\Http\Resources\UserResource;
use App\ModelFilters\Admin\AdminUserFilter;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
        $users = AdminUser::filter($request->all(), AdminUserFilter::class)->with(['roles'])->latest()->paginate($request->page_size ?? 15);

        return AdminUserResource::collection($users);
    }

    public function store(AdminUserRequest $request)
    {
        $user = new AdminUser(
            $request->only([
                'name', 'username', 'status'
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
                'name', 'username', 'status'
            ])
        );

        if ($password = $request->password) {
            $user->password = $password;
        }

        $user->roles()->sync($request->role_ids);

        $user->save();

        return AdminUserResource::make($user);
    }

    public function changeStatus(AdminUser $user)
    {
        if ($user->status === AdminUser::STATUS_ENABLE) {
            $user->status = AdminUser::STATUS_DISABLE;
            $user->token()?->revoke();
            $user->save();
        } else {
            $user->status = AdminUser::STATUS_ENABLE;
            $user->save();
        }

        return AdminUserResource::make($user);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        if (!Hash::check($request->old_password, $user->password)) {
            throw new InvalidRequestException('当前密码错误');
        }

        $user->password = $request->password;
        $user->save();

        return AdminUserResource::make($user);
    }

    public function destroy(AdminUser $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
