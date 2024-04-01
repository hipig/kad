<?php

namespace App\Notifications;

use App\Models\PostLike;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected PostLike $like
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
            'post' => $this->like->post->toArray()
        ];
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return $notifiable->id !== $this->like->user_id;
    }
}
