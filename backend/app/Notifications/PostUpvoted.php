<?php

namespace App\Notifications;

use App\Models\FeedbackPost;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostUpvoted extends Notification implements ShouldQueue
{
    use Queueable;

    private $vote;

    /**
     * Create a new notification instance.
     */
    public function __construct(Vote $vote)
    {
        $this->vote = $vote;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line("{$this->vote->user->name} upvoted your post titled: \"{$this->vote->feedbackPost->title}\"");
        // ->action('Notification Action', url('/'))
        // ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_name' => $this->vote->user->name,
            'post_id' => $this->vote->feedbackPost->id,
            'post_title' => $this->vote->feedbackPost->title
        ];
    }
}
