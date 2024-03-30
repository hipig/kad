<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthorizationRequest;
use App\Http\Resources\AdminUserResource;
use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthorizationsController extends Controller
{
    public function store(AuthorizationRequest $request)
    {
        $user = AdminUser::query()->where('username', $request->username)->where('status', AdminUser::STATUS_ENABLE)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new InvalidRequestException('用户名或密码错误');
        }
        $token = $user->createToken('User Login');

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return AdminUserResource::make($user);
    }

    public function destroy(Request $request)
    {
        $request->user()->token()->revoke();
        return response(null, 204);
    }

    protected function respondWithToken($token)
    {
        $model = $token->token;
        $model->expires_at = Carbon::now()->addDays(7);
        $model->save();

        return response()->json([
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_in' => $model->expires_at
        ]);
    }


}
