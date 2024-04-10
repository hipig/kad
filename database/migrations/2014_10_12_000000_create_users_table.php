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
            $table->unsignedInteger('friend_count')->default(0)->comment('好友数量');
            $table->unsignedInteger('following_count')->default(0)->comment('关注数量');
            $table->unsignedInteger('follower_count')->default(0)->comment('粉丝数量');
            $table->unsignedInteger('chat_group_count')->default(0)->comment('群组数量');
            $table->unsignedInteger('post_count')->default(0)->comment('动态数量');
            $table->string('homepage_cover')->nullable()->comment('主页封面');
            $table->string('gender', 64)->default('Gender_Type_Unknown')->comment('性别');
            $table->string('location')->nullable()->comment('所在地');
            $table->unsignedInteger('birthday')->default(0)->comment('生日');
            $table->string('self_signature')->nullable()->comment('个性签名');
            $table->string('allow_type', 64)->default('AllowType_Type_AllowAny')->comment('加好友验证方式');
            $table->unsignedInteger('language')->default(0)->comment('语言');
            $table->string('admin_forbid_type', 64)->default('AdminForbid_Type_None')->comment('管理员禁止加好友标识');
            $table->unsignedInteger('level')->default(0)->comment('等级');
            $table->unsignedInteger('role')->default(0)->comment('角色');
            $table->json('defined_data')->nullable()->comment('自定义字段');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态');
            $table->string('online_status', 64)->nullable()->comment('在线状态');
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
