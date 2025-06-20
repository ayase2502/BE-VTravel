<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('album_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('album_id')->nullable();
            $table->string('image_url')->nullable();
            $table->string('caption')->nullable();
            $table->timestamp('uploaded_at')->nullable()->useCurrent();

            $table->foreign('album_id')->references('album_id')->on('albums')->onDelete('set null');
            $table->index('album_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('album_images');
    }
};
