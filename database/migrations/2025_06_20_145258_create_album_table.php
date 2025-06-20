<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id('album_id');
            $table->string('title')->nullable();
            $table->timestamps(); // created_at & updated_at = CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
