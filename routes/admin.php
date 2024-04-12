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

    Route::apiResource('users', Admin\UsersController::class)->only(['index', 'store', 'update'])->names('users');
    Route::post('users/{user}/change-status', [Admin\UsersController::class, 'changeStatus'])->name('users.change-status');
    Route::post('users/change-password', [Admin\UsersController::class, 'changePassword'])->name('users.change-password');

    Route::get('reports', [Admin\ReportsController::class, 'index'])->name('reports.index');
    Route::post('reports/handle', [Admin\ReportsController::class, 'handle'])->name('reports.handle');

    Route::apiResource('posts', Admin\PostsController::class)->only(['index', 'destroy'])->names('posts');

    Route::apiResource('post-comments', Admin\PostCommentsController::class)->only(['index', 'destroy'])->names('post-comments')->parameters([
        'post-comments' => 'comment'
    ]);;

    Route::apiResource('chat-groups', Admin\ChatGroupsController::class)->names('chat-groups')->only(['index', 'store', 'update', 'show'])->parameters([
        'chat-groups' => 'group'
    ]);
    Route::post('chat-groups/dissolve', [Admin\ChatGroupsController::class, 'dissolve'])->name('chat-groups.dissolve');
    Route::post('chat-groups/{group}/join', [Admin\ChatGroupsController::class, 'join'])->name('chat-groups.join');
    Route::post('chat-groups/{group}/exit', [Admin\ChatGroupsController::class, 'exit'])->name('chat-groups.exit');
    Route::get('chat-group-users', [Admin\ChatGroupUsersController::class, 'index'])->name('chat-group-users.index');

    Route::post('chat-group-messages/send', [Admin\ChatGroupMessagesController::class, 'send'])->name('chat-group-messages.send');
    Route::post('chat-group-messages/recall', [Admin\ChatGroupMessagesController::class, 'recall'])->name('chat-group-messages.recall');
    Route::apiResource('chat-group-messages', Admin\ChatGroupMessagesController::class)->names('chat-group-messages')->parameters([
        'chat-group-messages' => 'message'
    ]);

    Route::post('chat-messages/withdraw', [Admin\ChatMessagesController::class, 'withdraw'])->name('chat-messages.withdraw');
    Route::apiResource('chat-messages', Admin\ChatMessagesController::class)->only(['index', 'store'])->names('chat-messages')->parameters([
        'chat-messages' => 'message'
    ]);
});
