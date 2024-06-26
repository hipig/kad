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

        $query = $user->notifications();

        $type = $request->input('type');

        switch ($type) {
            case 'Commented':
                $query->whereIn('type', ['PostCommented', 'PostCommentCommented']);
                break;
            case 'Liked':
                $query->whereIn('type', ['PostLiked', 'PostCommentLiked']);
                break;
        }

        if ($request->boolean('unread')) {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest('read_at')->paginate($request->page_size ?? 15);

        return NotificationResource::collection($notifications);
    }

    public function read(Request $request, DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return NotificationResource::make($notification);
    }
}
