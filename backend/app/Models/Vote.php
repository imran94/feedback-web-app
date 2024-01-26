<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'isUpvote'
    ];

    protected static function booted(): void
    {
        static::created(function (Vote $vote) {
            $post = $vote->feedbackPost;
            $post->vote_count += $vote->is_upvote ? 1 : -1;
            $post->save();
        });

        static::updated(function (Vote $vote) {
            $post = $vote->feedbackPost;
            $post->vote_count += $vote->is_upvote ? 2 : -2;
            $post->save();
        });

        static::deleted(function (Vote $vote) {
            $post = $vote->feedbackPost;
            $post->vote_count += $vote->is_upvote ? -1 : 1;
            $post->save();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackPost(): BelongsTo
    {
        return $this->belongsTo(FeedbackPost::class);
    }
}
