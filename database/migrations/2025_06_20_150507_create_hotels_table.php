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
        Schema::create('hotels', function (Blueprint $table) {
            $table->integer('hotel_id', true);
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('room_type', 100)->nullable();
            $table->decimal('price', 12)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('album_id')->nullable()->index('album_id');
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->comment('active = hoạt động, inactive = không hoạt động (ẩn)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
