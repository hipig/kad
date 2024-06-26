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

        Route::get('users/search', [V1\UsersController::class, 'search'])->name('users.search');
        Route::get('users/{user}', [V1\UsersController::class, 'show'])->name('users.show');
        Route::get('users/{user}/following', [V1\UserFollowersController::class, 'following'])->name('users.following');
        Route::get('users/{user}/followers', [V1\UserFollowersController::class, 'followers'])->name('users.followers');
        Route::post('users/{user}/follow', [V1\UserFollowersController::class, 'follow'])->name('users.follow');
        Route::post('users/{user}/un-follow', [V1\UserFollowersController::class, 'unFollow'])->name('users.un-follow');
        Route::post('users/{user}/report', [V1\UsersController::class, 'report'])->name('users.report');

        Route::apiResource('posts', V1\PostsController::class)->only(['index', 'store', 'show', 'destroy'])->names('posts');
        Route::post('posts/{post}/repost', [V1\PostsController::class, 'repost'])->name('posts.repost');
        Route::post('posts/{post}/comment', [V1\PostsController::class, 'comment'])->name('posts.comment');
        Route::post('posts/{post}/like', [V1\PostsController::class, 'like'])->name('posts.like');
        Route::post('posts/{post}/un-like', [V1\PostsController::class, 'unLike'])->name('posts.un-like');
        Route::post('posts/{post}/collect', [V1\PostsController::class, 'collect'])->name('posts.collect');
        Route::post('posts/{post}/un-collect', [V1\PostsController::class, 'unCollect'])->name('posts.un-collect');
        Route::post('posts/{post}/report', [V1\PostsController::class, 'report'])->name('posts.report');
        Route::post('posts/{post}/block', [V1\PostsController::class, 'block'])->name('posts.block');
        Route::post('posts/{post}/un-block', [V1\PostsController::class, 'unBlock'])->name('posts.un-block');
        Route::post('posts/{post}/set-top', [V1\PostsController::class, 'setTop'])->name('posts.set-top');
        Route::post('posts/{post}/set-visible', [V1\PostsController::class, 'setVisible'])->name('posts.set-visible');

        Route::delete('post-comments/{comment}', [V1\PostCommentsController::class, 'destroy'])->name('post-comments.destroy');
        Route::post('post-comments/{comment}/comment', [V1\PostCommentsController::class, 'comment'])->name('post-comments.comment');
        Route::post('post-comments/{comment}/like', [V1\PostCommentsController::class, 'like'])->name('post-comments.like');
        Route::post('post-comments/{comment}/un-like', [V1\PostCommentsController::class, 'unLike'])->name('post-comments.un-like');

        Route::get('notifications', [V1\NotificationsController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/read', [V1\NotificationsController::class, 'read'])->name('notifications.read');

        Route::post('uploads', [V1\UploadsController::class, 'store'])->name('uploads.store');
    });
});
