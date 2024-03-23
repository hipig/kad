<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\TencentIM\TLSSigAPIv2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function userSign(Request $request)
    {
        $user = Auth::user();
        $sign = app(TLSSigAPIv2::class)->genUserSig($user->username, 86400 * 7);

        return response()->json(['user_sign' => $sign]);
    }
}
