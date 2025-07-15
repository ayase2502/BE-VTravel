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
        Schema::create('tours', function (Blueprint $table) {
            $table->integer('tour_id', true);
            $table->integer('category_id')->nullable()->index('category_id');
            $table->integer('album_id')->nullable()->index('album_id');
            $table->string('tour_name');
            $table->text('description')->nullable();
            $table->text('itinerary')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 12);
            $table->decimal('discount_price', 12)->nullable();
            $table->string('destination')->nullable();
            $table->string('duration', 100)->nullable();
            $table->enum('status', ['visible', 'hidden'])->nullable()->default('visible');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->comment('active = hoạt động, inactive = không hoạt động (ẩn)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
