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
        Schema::create('reviews', function (Blueprint $table) {
            $table->integer('review_id', true);
            $table->integer('user_id')->nullable()->index('user_id');
            $table->integer('tour_id')->nullable()->index('tour_id');
            $table->integer('rating')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->comment('active = hoạt động, inactive = không hoạt động (ẩn)');
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tour_id')->references('tour_id')->on('tours')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
