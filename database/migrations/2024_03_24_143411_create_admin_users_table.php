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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->comment('管理员列表');
            $table->id();
            $table->string('username')->unique()->comment('用户名');
            $table->string('name')->comment('名称');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('password')->comment('密码');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
