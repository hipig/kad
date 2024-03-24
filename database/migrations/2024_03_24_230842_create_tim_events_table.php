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
        Schema::create('tim_events', function (Blueprint $table) {
            $table->comment('腾讯云即时通信事件');
            $table->id();
            $table->string('type', 64)->comment('类型');
            $table->json('data')->nullable()->comment('事件数据');
            $table->timestamp('executed_at')->nullable()->comment('执行时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tim_events');
    }
};
