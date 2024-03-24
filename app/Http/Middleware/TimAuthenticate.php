<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TimAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make($request->all(), [
            'Sign' => 'required',
            'RequestTime' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException('签名校验失败');
        }

        $sign = $request->input('Sign');

        $timestamp = $request->input('RequestTime');

        $time = Carbon::createFromTimestamp($timestamp);

        if (now()->subMinute()->gt($time) && now()->addMinute()->lt($time)) {
            throw new InvalidRequestException('签名校验失败');
        }

        $hashSign = hash('sha256', config('im.token') . $time);

        if (strcmp($sign, $hashSign) !== 0) {
            throw new InvalidRequestException('签名校验失败');
        }

        return $next($request);
    }
}
