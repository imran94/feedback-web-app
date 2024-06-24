<?php

namespace App\Http\Controllers;

use App\Models\FeedbackPost;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackPostController extends Controller
{

    public function getAll()
    {
        return FeedbackPost::with('user')
            ->withCount('comments')
            ->paginate();
    }

    public function getPostById(string $id)
    {
        return FeedbackPost::with(['user', 'comments.user'])
            ->find($id);
    }

    public function search(Request $request)
    {
        $q = $request->input('search');
        $categories = $request->input('categories');
        $res = FeedbackPost::with('user')
            ->withCount('comments');
        if (!empty($categories)) {
            $res = $res->whereIn('category', $categories);
        }
        // $res = $res->where('title', 'like', "%$q%")
        //     ->orWhere('description', 'like', "%$q%")
        //     ->orWhereHas('user', function ($query) use ($q) {
        //         $query->where('name', 'like', "%$q%");
        //     });

        $res = $res->where(function (Builder $query) use ($q) {
            $query->where('title', 'like', "%$q%")
                ->orWhere('description', 'like', "%$q%")
                ->orWhereHas('user', function ($query) use ($q) {
                    $query->where('name', 'like', "%$q%");
                });
        });

        return $res->paginate();
    }

    public function getByUser()
    {
        return Auth::user()
            ->feedbackPosts()
            ->with('user')
            ->paginate();
    }

    public function getAllByUser(User $user)
    {
        return $user
            ->feedbackPosts()
            ->with('user')
            ->paginate();
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
