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
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign(['user_id'], 'reviews_ibfk_1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['tour_id'], 'reviews_ibfk_2')->references(['tour_id'])->on('tours')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign('reviews_ibfk_1');
            $table->dropForeign('reviews_ibfk_2');
        });
    }
};
