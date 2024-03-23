<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nickname')->comment('昵称');
            $table->string('username')->unique()->comment('用户名');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('wallet_account')->unique()->comment('钱包地址');
            $table->unsignedInteger('following_count')->default(0)->comment('关注数量');
            $table->unsignedInteger('follower_count')->default(0)->comment('粉丝数量');
            $table->string('homepage_cover')->nullable()->comment('主页封面');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
