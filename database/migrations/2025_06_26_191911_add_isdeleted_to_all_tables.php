<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add isdeleted to tours
        Schema::table('tours', function (Blueprint $table) {
            $table->boolean('isdeleted')->default(false)->after('status');
        });

        // Add isdeleted to tour_categories
        Schema::table('tour_categories', function (Blueprint $table) {
            $table->boolean('isdeleted')->default(false)->after('thumbnail');
        });

        // Add isdeleted to album_images
        Schema::table('album_images', function (Blueprint $table) {
            $table->boolean('isdeleted')->default(false)->after('caption');
        });

        // Add isdeleted to albums
        Schema::table('albums', function (Blueprint $table) {
            $table->boolean('isdeleted')->default(false)->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn('isdeleted');
        });

        Schema::table('tour_categories', function (Blueprint $table) {
            $table->dropColumn('isdeleted');
        });

        Schema::table('album_images', function (Blueprint $table) {
            $table->dropColumn('isdeleted');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('isdeleted');
        });
    }
};
