<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedbackPostController;
use App\Http\Controllers\VoteController;
use App\Models\FeedbackPost;
use Illuminate\Auth\Events\Logout;
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

Route::post('/test-upload', [AuthController::class, 'testUpload']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/authenticate', [AuthController::class, 'authenticate']);
Route::delete('/user/logout', [AuthController::class, 'logout']);

Route::post('/user/forgot-password', [AuthController::class, 'forgotPassword']);
Route::put('/reset-password/{token}', [AuthController::class, 'resetPasswordWithToken']);

// Feedback Posts
Route::get('/feedback/{id}', [FeedbackPostController::class, 'getPostById']);
Route::get('/feedback', [FeedbackPostController::class, 'getAll']);

Route::get('/user/{user}', [AuthController::class, 'getUserById']);
Route::get('/user/{user}/feedback', [FeedbackPostController::class, 'getAllByUser']);

Route::middleware(
    'auth:sanctum',
    'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value
)->group(function () {
    Route::get('/user/refresh-token', [AuthController::class, 'refreshToken']);
});

Route::middleware(
    'auth:sanctum',
    'ability:' . TokenAbility::ACCESS_API->value
)->group(function () {
    Route::get('/user', [AuthController::class, 'getCurrentUser']);
    Route::post('/user/update', [AuthController::class, 'update']);
    Route::put('/user/reset-password', [AuthController::class, 'updatePassword']);
    Route::delete('/user/{user}', [AuthController::class, 'delete']);

    Route::post('/search', [FeedbackPostController::class, 'search']);
    Route::get('/user/feedback', [FeedbackPostController::class, 'getByUser']);
    Route::post('/feedback', [FeedbackPostController::class, 'create']);
    Route::put('/feedback/{feedbackPost}', [FeedbackPostController::class, 'update']);
    Route::delete('/feedback/{feedbackPost}', [FeedbackPostController::class, 'delete']);

    Route::post('/feedback/{feedbackPost}/comments', [CommentController::class, 'create']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'delete']);

    Route::put('/feedback/{post}/vote', [VoteController::class, 'vote']);
    Route::delete('/feedback/{postId}/vote', [VoteController::class, 'delete']);
    Route::get('/feedback/{post}/vote', [VoteController::class, 'getUserVote']);
});
