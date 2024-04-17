<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserCreated;
use App\Events\UserDisabled;
use App\Events\UserUpdated;
use App\Exceptions\InvalidRequestException;
use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\UserResource;
use App\ModelFilters\Admin\UserFilter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::filter($request->all(), UserFilter::class)->latest()->paginate($request->page_size ?? 15);

        return UserResource::collection($users);
    }

    public function store(UserRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create(
                $request->only([
                    'nickname', 'avatar', 'wallet_account'
                ])
            );

            event(new UserCreated($user));
            event(new UserUpdated($user));

            return $user;
        });

        return UserResource::make($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $user = DB::transaction(function () use ($request, $user) {
            $user->fill(
                $request->only([
                    'nickname', 'avatar', 'gender', 'birthday', 'location',
                    'self_signature', 'allow_type', 'allow_type', 'language',
                    'admin_forbid_type', 'level', 'role'
                ])
            );
            $user->save();

            event(new UserUpdated($user));

            return $user;
        });

        return UserResource::make($user);
    }

    public function changeStatus(User $user)
    {
        if ($user->status === User::STATUS_ENABLE) {
            $user->status = User::STATUS_DISABLE;
            $user->online_status = User::ONLINE_STATUS_LOGOUT;
            $user->token()?->revoke();
            $user->save();

            event(new UserDisabled($user));
        } else {
            $user->status = User::STATUS_ENABLE;
            $user->save();
        }

        return UserResource::make($user);
    }

    public function export(Request $request)
    {
        return (new UserExport($request->all()))->download('用户列表.xlsx');
    }
}
