<?php

namespace App\Notifications;

use App\Models\PostCommentLike;
use App\Models\PostLike;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostCommentLiked extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected PostCommentLike $like
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user' => $this->like->user->toArray(),
            'comment' => $this->like->comment->toArray()
        ];
    }

    public function databaseType()
    {
        return 'PostCommentLiked';
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return $notifiable->id !== $this->like->user_id;
    }
}
