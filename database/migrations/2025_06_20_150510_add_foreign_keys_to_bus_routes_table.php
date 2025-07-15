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
        Schema::table('bus_routes', function (Blueprint $table) {
            $table->foreign(['album_id'], 'bus_routes_ibfk_1')->references(['album_id'])->on('albums')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_routes', function (Blueprint $table) {
            $table->dropForeign('bus_routes_ibfk_1');
        });
    }
};
