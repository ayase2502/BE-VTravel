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
        Schema::table('combos', function (Blueprint $table) {
            $table->foreign(['tour_id'], 'combos_ibfk_1')->references(['tour_id'])->on('tours')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['hotel_id'], 'combos_ibfk_2')->references(['hotel_id'])->on('hotels')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['transportation_id'], 'combos_ibfk_3')->references(['transportation_id'])->on('transportations')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('combos', function (Blueprint $table) {
            $table->dropForeign('combos_ibfk_1');
            $table->dropForeign('combos_ibfk_2');
            $table->dropForeign('combos_ibfk_3');
        });
    }
};
