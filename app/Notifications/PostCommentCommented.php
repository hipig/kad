<?php

namespace App\Notifications;

use App\Models\PostComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostCommentCommented extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected PostComment $comment
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
            'content' => $this->comment->content,
            'user' => $this->comment->user->toArray(),
            'comment' => $this->comment->comment->toArray(),
            'post' => $this->comment->post->toArray()
        ];
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return $notifiable->id !== $this->comment->user_id;
    }
}
