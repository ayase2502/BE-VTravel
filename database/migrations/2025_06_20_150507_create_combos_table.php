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
        Schema::create('combos', function (Blueprint $table) {
            $table->integer('combo_id', true);
            $table->integer('tour_id')->nullable()->index('tour_id');
            $table->integer('hotel_id')->nullable()->index('hotel_id');
            $table->string('image')->nullable();
            $table->integer('transportation_id')->nullable()->index('transportation_id');
            $table->decimal('price', 12)->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combos');
    }
};
