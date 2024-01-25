<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function vote(Request $request, string $postId)
    {
        Vote::upsert(
            [
                [
                    'user_id' => Auth::user()->id,
                    'feedback_post_id' => $postId,
                    'is_upvote' => $request->input('isUpvote')
                ]
            ],
            ['user_id', 'feedback_post_id'],
            ['is_upvote']
        );
        return response()->json();
    }

    public function delete(string $postId)
    {
        Vote::where([
            'user_id' => Auth::user()->id,
            'feedback_post_id' => $postId
        ])->delete();

        return response()->json();
    }
}
