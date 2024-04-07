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
        Schema::create('chat_group_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->comment('群组ID');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->string('role', 64)->default('Member')->comment('角色');
            $table->unsignedInteger('msg_seq')->nullable()->comment('该成员当前已读消息');
            $table->string('msg_flag', 64)->nullable()->comment('消息接收选项');
            $table->timestamp('last_send_msg_at')->nullable()->comment('最后一次发消息的时间');
            $table->timestamp('mute_at')->nullable()->comment('禁言截至时间');
            $table->timestamp('join_at')->comment('加入时间');
            $table->json('defined_data')->nullable()->comment('自定义字段');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_group_users');
    }
};
