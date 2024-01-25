<?php

namespace App\Policies;

use App\Models\FeedbackPost;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FeedbackPostPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function modify(User $user, FeedbackPost $post): bool
    {
        return $user->id === $post->user_id || $user->is_admin;
    }
}
