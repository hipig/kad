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
        Schema::create('posts', function (Blueprint $table) {
            $table->comment('动态');
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedBigInteger('repost_post_id')->nullable()->comment('转发动态ID');
            $table->text('content')->nullable()->comment('发布内容');
            $table->unsignedTinyInteger('visible_status')->default(1)->comment('可见状态');
            $table->unsignedInteger('view_count')->default(0)->comment('浏览数量');
            $table->unsignedInteger('collect_count')->default(0)->comment('收藏数量');
            $table->unsignedInteger('like_count')->default(0)->comment('点赞数量');
            $table->unsignedInteger('comment_count')->default(0)->comment('评论数量');
            $table->unsignedInteger('repost_count')->default(0)->comment('转发数量');
            $table->timestamp('top_at')->nullable()->comment('置顶时间');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
