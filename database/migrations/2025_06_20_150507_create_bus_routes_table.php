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
        Schema::create('bus_routes', function (Blueprint $table) {
            $table->integer('route_id', true);
            $table->string('route_name')->nullable();
            $table->string('vehicle_type', 100)->nullable();
            $table->decimal('price', 12)->nullable();
            $table->integer('seats')->nullable();
            $table->integer('album_id')->nullable()->index('album_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_routes');
    }
};
