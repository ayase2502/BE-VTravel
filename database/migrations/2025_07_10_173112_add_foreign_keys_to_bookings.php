<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tour_id')->references('tour_id')->on('tours')->onDelete('cascade');
            $table->foreign('guide_id')->references('guide_id')->on('guides')->onDelete('set null');
            $table->foreign('hotel_id')->references('hotel_id')->on('hotels')->onDelete('set null');
            $table->foreign('bus_route_id')->references('route_id')->on('bus_routes')->onDelete('set null');
            $table->foreign('motorbike_id')->references('bike_id')->on('motorbikes')->onDelete('set null');
            $table->foreign('custom_tour_id')->references('custom_tour_id')->on('custom_tour')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['tour_id']);
            $table->dropForeign(['guide_id']);
            $table->dropForeign(['hotel_id']);
            $table->dropForeign(['bus_route_id']);
            $table->dropForeign(['motorbike_id']);
            $table->dropForeign(['custom_tour_id']);
        });
    }
};