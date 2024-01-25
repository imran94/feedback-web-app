<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedbackPostController;
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

Route::middleware('auth:sanctum')->get('/user', function () {
    return Auth::user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/authenticate', [AuthController::class, 'authenticate']);

// Feedback Posts
Route::get('/feedback/{id}', [FeedbackPostController::class, 'getPostById']);
Route::get('/feedback', [FeedbackPostController::class, 'getAll']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/feedback', [FeedbackPostController::class, 'create']);
    Route::put('/feedback/{id}', [FeedbackPostController::class, 'update']);
    Route::delete('/feedback/{id}', [FeedbackPostController::class, 'delete']);
});
