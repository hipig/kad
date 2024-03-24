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
        Schema::create('post_has_images', function (Blueprint $table) {
            $table->comment('动态内图片');
            $table->id();
            $table->unsignedBigInteger('post_id')->comment('动态ID');
            $table->unsignedBigInteger('upload_id')->comment('图片ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_has_images');
    }
};
