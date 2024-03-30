<?php

namespace App\Http\Controllers\Admin;

use App\Events\TimEventCreated;
use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Models\TimEvent;
use Illuminate\Http\Request;

class TimController extends Controller
{
    public function callback(Request $request)
    {
        if ($request->SdkAppid !== config('im.appid')) {
            throw new InvalidRequestException('SdkAppid 错误');
        }

        $event = TimEvent::create([
            'type' => $request->CallbackCommand,
            'platform' => $request->OptPlatform,
            'client_ip' => $request->ClientIP,
            'data' => $request->post()
        ]);

        event(new TimEventCreated($event));

        return response()->json([
            'ActionStatus' => 'OK',
            'ErrorInfo' => '',
            'ErrorCode' => 0
        ]);
    }
}
