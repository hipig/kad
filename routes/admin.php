<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// 登录
Route::post('authorizations', [Admin\AuthorizationsController::class, 'store'])->name('authorizations.store');

// 当前菜单
Route::get('menus/current', [Admin\MenusController::class, 'current'])->name('menus.current');

Route::middleware('auth:admin_api')->group(function () {

    Route::get('me', [Admin\AuthorizationsController::class, 'me'])->name('authorizations.me');

    Route::delete('authorizations', [Admin\AuthorizationsController::class, 'destroy'])->name('authorizations.destroy');

    Route::apiResource('users', Admin\UsersController::class)->names('users');
});
