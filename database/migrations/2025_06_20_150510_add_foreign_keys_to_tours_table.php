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
        Schema::table('tours', function (Blueprint $table) {
            $table->foreign(['album_id'], 'tours_ibfk_1')->references(['album_id'])->on('albums')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['category_id'], 'tours_ibfk_2')->references(['category_id'])->on('tour_categories')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropForeign('tours_ibfk_1');
            $table->dropForeign('tours_ibfk_2');
        });
    }
};
