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
        Schema::create('uploads', function (Blueprint $table) {
            $table->comment('上传文件');
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('用户ID');
            $table->string('name')->comment('名称');
            $table->string('disk')->comment('磁盘');
            $table->string('path')->comment('路径');
            $table->string('type', 64)->default('image')->comment('类型');
            $table->string('extension', 64)->comment('后缀');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
