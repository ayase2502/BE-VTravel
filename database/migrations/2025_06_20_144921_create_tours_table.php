<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id('tour_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('album_id')->nullable();
            $table->string('tour_name');
            $table->text('description')->nullable();
            $table->text('itinerary')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('destination')->nullable();
            $table->string('duration', 100)->nullable();
            $table->enum('status', ['visible', 'hidden'])->default('visible');
            $table->timestamps(); // Includes created_at and updated_at with auto-update

            $table->foreign('album_id')->references('album_id')->on('albums')->onDelete('set null');
            $table->foreign('category_id')->references('category_id')->on('tour_categories')->onDelete('set null');

            $table->index('album_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
