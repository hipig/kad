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
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->comment('聊天群组');
            $table->id();
            $table->unsignedBigInteger('owner_id')->nullable()->comment('群主用户ID');
            $table->string('group_id')->comment('群组ID');
            $table->string('name')->comment('名称');
            $table->string('type', 64)->default('public')->comment('类型');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('introduction')->nullable()->comment('简介');
            $table->string('notification')->nullable()->comment('通知');
            $table->timestamp('last_info_time')->nullable()->comment('最后群资料变更时间');
            $table->timestamp('last_msg_time')->nullable()->comment('群内最后一条消息的时间');
            $table->unsignedInteger('member_num')->default(0)->comment('当前群成员数量');
            $table->unsignedInteger('max_member_num')->default(0)->comment('最大群成员数量');
            $table->string('apply_join_option', 64)->default('FreeAccess')->comment('申请加群处理方式');
            $table->string('invite_join_option', 64)->default('FreeAccess')->comment('邀请加群处理方式');
            $table->string('mute_all_member', 64)->default('On')->comment('群全员禁言状态');
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
        Schema::dropIfExists('chat_groups');
    }
};
