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
        Schema::create('menus', function (Blueprint $table) {
            $table->comment('菜单');
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->comment('上级ID');
            $table->string('type', 64)->comment('类型');
            $table->string('name')->comment('名称');
            $table->string('key')->comment('标识');
            $table->string('path')->nullable()->comment('路径');
            $table->string('icon')->nullable()->comment('图标');
            $table->boolean('is_visible')->default(true)->comment('是否可见');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1-启用 2-禁用');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
