<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\FeedbackPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function getAll()
    {
        return response()->json(Comment::all());
    }

    public function getCommentById(Comment $comment)
    {
        return response()->json($comment);
    }

    public function create(Request $request, FeedbackPost $feedbackPost)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $comment = new Comment;
        $comment->content = $request->input('content');
        $comment->user()->associate(Auth::user());
        $comment->feedbackPost()->associate($feedbackPost);
        $comment->save();
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('modify', $comment);

        $request->validate([
            'content' => 'required|string'
        ]);
        $comment->content = $request->input('content');
        $comment->save();
    }

    public function delete(Comment $comment)
    {
        $this->authorize('modify', $comment);
        $comment->delete();
        return response()->json();
    }
}
