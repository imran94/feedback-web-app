<?php

namespace App\Http\Controllers;

use App\Models\FeedbackPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackPostController extends Controller
{

    public function getAll()
    {
        return response()->json(FeedbackPost::with('user')->paginate());
    }

    public function getPostById(string $id)
    {
        return response()->json(FeedbackPost::with(['user', 'comments.user'])->find($id));
    }

    public function getByUser()
    {
        return response()->json(Auth::user()->feedbackPosts()->with('user')->paginate());
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

        return response()->json($post);
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
        return response()->json($feedbackPost);
    }

    public function delete(FeedbackPost $feedbackPost)
    {
        $this->authorize('modify', $feedbackPost);
        $feedbackPost->delete();

        return response()->json();
    }
}
