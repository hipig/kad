<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    public function store(Request $request)
    {
        $walletAccount = $request->wallet_account;
        $this->validate($request, [
            'wallet_account' => 'required'
        ]);

        $user = User::query()->where('wallet_account', $walletAccount)->first();
        if (!$user) {
            $user = new User([
                'wallet_account' => $walletAccount
            ]);
            $user->save();
            $user->refresh();

            event(new UserCreated($user));
        }

        event(new UserUpdated($user));

        if ($user->status != User::STATUS_ENABLE) {
            throw new InvalidRequestException('用户已禁用');
        }

        $token = $user->createToken('User Login');

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function me(Request $request)
    {
        return UserResource::make($request->user());
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

    public function destroy(Request $request)
    {
        $request->user()->token()->revoke();
        return response(null, 204);
    }
}
