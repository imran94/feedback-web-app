<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedbackPostController;
use App\Http\Controllers\VoteController;
use App\Models\FeedbackPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/register', [AuthController::class, 'register']);
Route::post('/authenticate', [AuthController::class, 'authenticate']);

// Feedback Posts
Route::get('/feedback/{id}', [FeedbackPostController::class, 'getPostById']);
Route::get('/feedback', [FeedbackPostController::class, 'getAll']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function () {
        return Auth::user();
    });

    Route::post('/feedback', [FeedbackPostController::class, 'create']);
    Route::put('/feedback/{feedbackPost}', [FeedbackPostController::class, 'update']);
    Route::delete('/feedback/{feedbackPost}', [FeedbackPostController::class, 'delete']);
    Route::get('/user/feedback', [FeedbackPostController::class, 'getByUser']);

    Route::post('/feedback/{feedbackPost}/comments', [CommentController::class, 'create']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'delete']);

    Route::put('/feedback/{post}/vote', [VoteController::class, 'vote']);
    Route::delete('/feedback/{postId}/vote', [VoteController::class, 'delete']);
    Route::get('/feedback/{post}/vote', [VoteController::class, 'getUserVote']);
});
