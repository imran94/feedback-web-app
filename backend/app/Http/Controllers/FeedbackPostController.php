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

    public function getPostById(string $id)
    {
        $post = FeedbackPost::find($id);
        if (is_null($post)) {
            return response()->json(["message" => "Post not found."], 404);
        }
        return response()->json($post);
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
        $post->user = Auth::user();
        $post->save();
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string',
            'category' => 'required|string|max:50'
        ]);

        $post = FeedbackPost::find($id);
        if (is_null($post)) {
            return response()->json(["message" => "Post not found."], 404);
        }

        $this->authorize('modify', $post);

        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $post->category = $request->input('category');
        $post->save();
    }

    public function delete(string $id)
    {
        $post = FeedbackPost::find($id);
        if (is_null($post)) {
            return response()->json(["message" => "Post not found."], 404);
        }
        $this->authorize('modify', $post);

        return response()->json();
    }
}
