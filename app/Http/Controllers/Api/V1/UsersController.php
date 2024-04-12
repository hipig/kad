<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Http\Resources\UserResource;
use App\Models\Report;
use App\Models\User;
use App\TencentIM\TLSSigAPIv2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function show(Request $request, User $user)
    {
        return UserResource::make($user);
    }

    public function report(Request $request, User $user)
    {
        $this->validate($request, [
            'type' => 'required'
        ]);

        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            throw new InvalidRequestException('怎么还有人举报自己啊');
        }

        $report = new Report(
            $request->only([
                'type',
                'content'
            ])
        );
        $report->user()->associate($authUser);
        $report->reportable()->associate($user);
        $report->save();

        return ReportResource::make($report);
    }

    public function userSign(Request $request)
    {
        $user = Auth::user();
        if ($user->status != User::STATUS_ENABLE) {
            throw new InvalidRequestException('用户已禁用');
        }

        $sign = app(TLSSigAPIv2::class)->genUserSig($user->username, 86400 * 7);
        return response()->json(['user_sign' => $sign]);
    }
}
