<?php

namespace App\Http\Controllers;

use App\Models\FeedbackPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackPostController extends Controller
{

    public function getAll()
    {
        return response()->json(FeedbackPost::all());
    }

    public function getPostById(FeedbackPost $feedbackPost)
    {
        return response()->json($feedbackPost);
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string',
            'category' => 'required|string|max:50'
        ]);

        $post = new FeedbackPost;
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $post->category = $request->input('category');
        $post->user()->associate(Auth::user());
        $post->save();
    }

    public function update(Request $request, FeedbackPost $feedbackPost)
    {
        $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string',
            'category' => 'required|string|max:50'
        ]);

        $this->authorize('modify', $feedbackPost);

        $feedbackPost->title = $request->input('title');
        $feedbackPost->description = $request->input('description');
        $feedbackPost->category = $request->input('category');
        $feedbackPost->save();
    }

    public function delete(FeedbackPost $feedbackPost)
    {
        $this->authorize('modify', $feedbackPost);
        $feedbackPost->delete();

        return response()->json();
    }
}
