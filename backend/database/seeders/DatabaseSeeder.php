<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Comment;
use App\Models\FeedbackPost;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Log::info('Creating users');
        User::factory(50)
            ->hasFeedbackPosts(5)
            ->create();

        $posts = FeedbackPost::all();
        $users = User::all();

        foreach ($posts as $post) {
            for ($i = 0; $i < 15; $i++) {
                $comment = new Comment(['content' => fake()->paragraphs(1, true)]);
                $comment->user()->associate(User::inRandomOrder()->first());
                $post->comments()->save($comment);
            }
        }

        foreach ($posts as $post) {
            foreach ($users as $user) {
                $vote = new Vote(['is_upvote' => fake()->boolean()]);
                $vote->user()->associate($user);
                $vote->feedbackPost()->associate($post);
                $vote->save();
            }
        }
    }
}
