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
            $table->foreign(['custom_tour_id'], 'custom_tour_details_ibfk_1')->references(['custom_tour_id'])->on('custom_tour')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['destination_id'], 'custom_tour_details_ibfk_2')->references(['destination_id'])->on('destinations')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['hotel_id'], 'custom_tour_details_ibfk_3')->references(['hotel_id'])->on('hotels')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['transportation_id'], 'custom_tour_details_ibfk_4')->references(['transportation_id'])->on('transportations')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['motorbike_id'], 'custom_tour_details_ibfk_5')->references(['bike_id'])->on('motorbikes')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['guide_id'], 'custom_tour_details_ibfk_6')->references(['guide_id'])->on('guides')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['bus_route_id'], 'custom_tour_details_ibfk_7')->references(['route_id'])->on('bus_routes')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_tour_details', function (Blueprint $table) {
            $table->dropForeign('custom_tour_details_ibfk_1');
            $table->dropForeign('custom_tour_details_ibfk_2');
            $table->dropForeign('custom_tour_details_ibfk_3');
            $table->dropForeign('custom_tour_details_ibfk_4');
            $table->dropForeign('custom_tour_details_ibfk_5');
            $table->dropForeign('custom_tour_details_ibfk_6');
            $table->dropForeign('custom_tour_details_ibfk_7');
        });
    }
};
