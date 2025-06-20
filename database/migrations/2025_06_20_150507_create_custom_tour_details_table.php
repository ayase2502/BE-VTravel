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
        Schema::create('custom_tour_details', function (Blueprint $table) {
            $table->integer('detail_id', true);
            $table->integer('custom_tour_id')->nullable()->index('custom_tour_id');
            $table->integer('destination_id')->nullable()->index('destination_id');
            $table->integer('hotel_id')->nullable()->index('hotel_id');
            $table->integer('transportation_id')->nullable()->index('transportation_id');
            $table->integer('motorbike_id')->nullable()->index('motorbike_id');
            $table->integer('guide_id')->nullable()->index('guide_id');
            $table->integer('bus_route_id')->nullable()->index('bus_route_id');
            $table->integer('quantity')->nullable()->default(1);
            $table->decimal('price', 12)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_tour_details');
    }
};
