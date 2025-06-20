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
        Schema::table('album_images', function (Blueprint $table) {
            $table->foreign(['album_id'], 'album_images_ibfk_1')->references(['album_id'])->on('albums')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('album_images', function (Blueprint $table) {
            $table->dropForeign('album_images_ibfk_1');
        });
    }
};
