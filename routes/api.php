<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1;

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

Route::prefix('v1')->name('api.v1.')->group(function () {

    Route::post('authorizations', [V1\AuthorizationsController::class, 'store'])->name('authorizations.store');

    Route::middleware('auth:api')->group(function () {

        Route::get('me', [V1\AuthorizationsController::class, 'me'])->name('authorizations.me');
        Route::get('user_sign', [V1\UsersController::class, 'userSign'])->name('users.user_sign');

        Route::get('users/{user}/following', [V1\UserFollowersController::class, 'following'])->name('users.following');
        Route::get('users/{user}/followers', [V1\UserFollowersController::class, 'followers'])->name('users.followers');
        Route::post('users/{user}/follow', [V1\UserFollowersController::class, 'follow'])->name('users.follow');
        Route::post('users/{user}/un-follow', [V1\UserFollowersController::class, 'unFollow'])->name('users.un-follow');

        Route::apiResource('posts', V1\PostsController::class)->only(['index', 'store', 'destroy'])->names('posts');
        Route::post('posts/{post}/comment', [V1\PostsController::class, 'comment'])->name('posts.comment');
        Route::post('posts/{post}/like', [V1\PostsController::class, 'like'])->name('posts.like');
        Route::post('posts/{post}/collect', [V1\PostsController::class, 'collect'])->name('posts.collect');

        Route::delete('post-comments/{comment}', [V1\PostCommentsController::class, 'destroy'])->name('post-comments.destroy');
    });
});
