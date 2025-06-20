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
        Schema::create('transportations', function (Blueprint $table) {
            $table->integer('transportation_id', true);
            $table->string('type', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->decimal('price', 12)->nullable();
            $table->integer('album_id')->nullable()->index('album_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transportations');
    }
};
