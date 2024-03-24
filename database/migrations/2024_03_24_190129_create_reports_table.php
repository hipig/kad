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
        Schema::create('reports', function (Blueprint $table) {
            $table->comment('举报');
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->string('type', 64)->comment('举报类型');
            $table->text('content')->nullable()->comment('描述内容');
            $table->string('handle_type', 64)->nullable()->comment('处理方式');
            $table->text('handle_remark')->nullable()->comment('处理备注');
            $table->timestamp('handled_at')->nullable()->comment('处理时间');
            $table->morphs('reportable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
