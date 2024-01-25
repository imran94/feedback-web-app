<?php

namespace App\Observers;

use App\Models\Vote;
use Illuminate\Support\Facades\Log;

class VoteObserver
{
    /**
     * Handle the Vote "created" event.
     */
    public function created(Vote $vote): void
    {
        Log::debug("Vote created. " . $vote->is_upvote);
        $post = $vote->feedbackPost;
        $post->voteCount += $vote->is_upvote ? 1 : -1;
        $post->save();
        Log::debug("Saved post having " . $post->voteCount . " votes");
    }

    /**
     * Handle the Vote "updated" event.
     */
    public function updated(Vote $vote): void
    {
        Log::debug("Vote updated. " . $vote->is_upvote);
        $post = $vote->feedbackPost;
        $post->feedbackPost->voteCount += $vote->is_upvote ? 2 : -2;
        $post->save();
        Log::debug("Saved post having " . $post->voteCount . " votes");
    }

    /**
     * Handle the Vote "deleted" event.
     */
    public function deleted(Vote $vote): void
    {
        Log::debug("Vote deleted. " . $vote->is_upvote);
        $post = $vote->feedbackPost;
        $post->feedbackPost->voteCount += $vote->is_upvote ? -1 : 1;
        $post->save();
        Log::debug("Saved post having " . $post->voteCount . " votes");
    }

    /**
     * Handle the Vote "restored" event.
     */
    public function restored(Vote $vote): void
    {
        //
    }

    /**
     * Handle the Vote "force deleted" event.
     */
    public function forceDeleted(Vote $vote): void
    {
        //
    }
}
