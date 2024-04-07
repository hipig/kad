<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::post('tim/callback', [Admin\TimController::class, 'callback'])->name('tim.callback');

// 登录
Route::post('authorizations', [Admin\AuthorizationsController::class, 'store'])->name('authorizations.store');

// 当前菜单
Route::get('menus/current', [Admin\MenusController::class, 'current'])->name('menus.current');

Route::post('uploads', [Admin\UploadsController::class, 'store'])->name('uploads.store');

Route::middleware('auth:admin_api')->group(function () {

    Route::get('me', [Admin\AuthorizationsController::class, 'me'])->name('authorizations.me');

    Route::delete('authorizations', [Admin\AuthorizationsController::class, 'destroy'])->name('authorizations.destroy');

    Route::apiResource('users', Admin\UsersController::class)->names('users');

    Route::get('reports', [Admin\ReportsController::class, 'index'])->name('reports.index');

    Route::apiResource('posts', Admin\PostsController::class)->names('posts');

    Route::apiResource('post-comments', Admin\PostCommentsController::class)->names('post-comments');

    Route::apiResource('chat-groups', Admin\ChatGroupsController::class)->names('chat-groups')->parameters([
        'chat-groups' => 'group'
    ]);
    Route::post('chat-groups/dissolve', [Admin\ChatGroupsController::class, 'dissolve'])->name('chat-groups.dissolve');
    Route::get('chat-group-users', [Admin\ChatGroupUsersController::class, 'index'])->name('chat-group-users.index');
});
