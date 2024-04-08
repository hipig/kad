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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->comment('单聊消息');
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('用户ID');
            $table->unsignedBigInteger('to_user_id')->comment('接收用户ID');
            $table->unsignedInteger('random')->comment('随机数');
            $table->unsignedBigInteger('msg_seq')->nullable()->comment('消息序列号');
            $table->string('msg_key', 64)->nullable()->comment('消息唯一标识');
            $table->json('body')->comment('消息体');
            $table->unsignedTinyInteger('online_only_flag')->default(0)->comment('在线消息');
            $table->timestamp('sent_at')->nullable()->comment('发送时间');
            $table->json('custom_data')->nullable()->comment('自定义数据');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
