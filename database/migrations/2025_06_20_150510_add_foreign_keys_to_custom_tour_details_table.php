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
        Schema::table('custom_tour_details', function (Blueprint $table) {
            $table->foreign('custom_tour_id')->references('custom_tour_id')->on('custom_tours')->onDelete('cascade');
            $table->foreign('destination_id')->references('destination_id')->on('destinations')->onDelete('set null');
            $table->foreign('hotel_id')->references('hotel_id')->on('hotels')->onDelete('set null');
            $table->foreign('transportation_id')->references('transportation_id')->on('transportations')->onDelete('set null');
            $table->foreign('motorbike_id')->references('bike_id')->on('motorbikes')->onDelete('set null');
            $table->foreign('guide_id')->references('guide_id')->on('guides')->onDelete('set null');
            $table->foreign('bus_route_id')->references('route_id')->on('bus_routes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('custom_tour_details', function (Blueprint $table) {
            $table->dropForeign(['custom_tour_id']);
            $table->dropForeign(['destination_id']);
            $table->dropForeign(['hotel_id']);
            $table->dropForeign(['transportation_id']);
            $table->dropForeign(['motorbike_id']);
            $table->dropForeign(['guide_id']);
            $table->dropForeign(['bus_route_id']);
        });
    }
};
