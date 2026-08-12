<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/suggestions', [UserController::class, 'suggestions']);
    Route::get('/users/{userId}', [UserController::class, 'show'])->whereNumber('userId');

    Route::post('/users/{userId}/follow', [FollowController::class, 'store'])->whereNumber('userId');
    Route::delete('/users/{userId}/follow', [FollowController::class, 'destroy'])->whereNumber('userId');
    Route::get('/users/{userId}/followers', [FollowController::class, 'followers'])->whereNumber('userId');
    Route::get('/users/{userId}/following', [FollowController::class, 'following'])->whereNumber('userId');

    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{id}', [PostController::class, 'show'])->whereNumber('id');
    Route::put('/posts/{id}', [PostController::class, 'update'])->whereNumber('id');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->whereNumber('id');

    Route::post('/posts/{postId}/like', [LikeController::class, 'store'])->whereNumber('postId');
    Route::delete('/posts/{postId}/like', [LikeController::class, 'destroy'])->whereNumber('postId');

    Route::get('/posts/{postId}/comments', [CommentController::class, 'index'])->whereNumber('postId');
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store'])->whereNumber('postId');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->whereNumber('id');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->whereNumber('id');

    Route::get('/feed', [FeedController::class, 'index']);

    Route::get('/stories', [StoryController::class, 'index']);
    Route::post('/stories', [StoryController::class, 'store']);
    Route::get('/stories/{id}', [StoryController::class, 'show'])->whereNumber('id');
    Route::delete('/stories/{id}', [StoryController::class, 'destroy'])->whereNumber('id');
});
