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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->index('user_id');
            $table->enum('related_type', ['tour', 'combo', 'hotel', 'transport', 'motorbike', 'guide', 'bus', 'other'])->nullable()->default('other');
            $table->integer('related_id')->nullable();
            $table->text('content');
            $table->integer('rating')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->comment('active = hoạt động, inactive = không hoạt động (ẩn)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
