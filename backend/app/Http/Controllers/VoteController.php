<?php

namespace App\Http\Controllers;

use App\Models\FeedbackPost;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function getUserVote(FeedbackPost $post)
    {
        $userVote = $post->votes()->where('user_id', Auth::user()->id)->first();
        if (is_null($userVote)) {
            return response()->json([], 404);
        }

        $userVote->is_upvote = $userVote->is_upvote === 1 ? true : false;
        return response()->json($userVote);
    }

    public function vote(Request $request, FeedbackPost $post)
    {
        $vote = Vote::where([
            'user_id' => Auth::user()->id,
            'feedback_post_id' => $post->id,
        ])->first();

        if (is_null($vote)) {
            $vote = new Vote;
            $vote->user()->associate(Auth::user());
            $vote->feedbackPost()->associate($post);
        }

        if ($vote->is_upvote !== null && $vote->is_upvote == $request->input('isUpvote')) {
            $vote->vote_count = $post->vote_count;
            $vote->is_upvote = $request->input('isUpvote');
            return response()->json($vote);
        }

        $vote->is_upvote = $request->input('isUpvote');
        $vote->save();
        $vote->vote_count = $post->fresh()->vote_count;
        return response()->json($vote);
    }

    public function delete(string $postId)
    {
        $vote = Vote::where([
            'user_id' => Auth::user()->id,
            'feedback_post_id' => $postId
        ])->first();

        if (isset($vote)) {
            $vote->delete();
        }

        return response()->json([
            'vote_count' => FeedbackPost::find($postId)->vote_count
        ]);
    }
}
