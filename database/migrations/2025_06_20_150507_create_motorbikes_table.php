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
        Schema::create('motorbikes', function (Blueprint $table) {
            $table->integer('bike_id', true);
            $table->string('bike_type', 100)->nullable();
            $table->decimal('price_per_day', 12)->nullable();
            $table->string('location')->nullable();
            $table->integer('album_id')->nullable()->index('album_id');
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->comment('active = hoạt động, inactive = không hoạt động (ẩn)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbikes');
    }
};
