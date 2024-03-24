<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $notifications = $user->notifications()->latest('read_at')->paginate($request->page_size ?? 15);

        return NotificationResource::collection($notifications);
    }

    public function read(Request $request, DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return NotificationResource::make($notification);
    }
}
