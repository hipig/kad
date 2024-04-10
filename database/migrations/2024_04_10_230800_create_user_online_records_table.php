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
        Schema::create('user_online_records', function (Blueprint $table) {
            $table->comment('用户在线记录');
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->string('action', 64)->comment('在线状态');
            $table->string('reason', 64)->comment('上下线原因');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_online_records');
    }
};
