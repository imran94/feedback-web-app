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
        Log::debug('Vote created');
        $post = $vote->feedbackPost;
        Log::debug('Post vote count before: ' . $post->vote_count);
        $post->vote_count += $vote->is_upvote ? 1 : -1;
        $res = $post->save();
        Log::debug('Post vote count after: ' . $post->vote_count . '. Result: ' . $res);
    }

    /**
     * Handle the Vote "updated" event.
     */
    public function updated(Vote $vote): void
    {
        Log::debug('Vote udpated');
        $post = $vote->feedbackPost;
        Log::debug('Post vote count before: ' . $post->vote_count);
        $post->vote_count += $vote->is_upvote ? 2 : -2;
        $res = $post->save();
        Log::debug('Post vote count after: ' . $post->vote_count . '. Result: ' . $res);
    }

    /**
     * Handle the Vote "deleted" event.
     */
    public function deleted(Vote $vote): void
    {
        Log::debug('Vote deleted');
        $post = $vote->feedbackPost;
        Log::debug('Post vote count before: ' . $post->vote_count);
        $post->vote_count += $vote->is_upvote ? -1 : 1;
        $res = $post->save();
        Log::debug('Post vote count after: ' . $post->vote_count . '. Result: ' . $res);
    }

    /**
     * Handle the Vote "restored" event.
     */
    public function restored(Vote $vote): void
    {
        Log::debug('Vote restored');
    }

    /**
     * Handle the Vote "force deleted" event.
     */
    public function forceDeleted(Vote $vote): void
    {
        Log::debug('Vote forceDeleted');
        $post = $vote->feedbackPost;
        Log::debug('Post vote count before: ' . $post->vote_count);
        $post->vote_count += $vote->is_upvote ? -1 : 1;
        $res = $post->save();
        Log::debug('Post vote count after: ' . $post->vote_count . '. Result: ' . $res);
    }
}
