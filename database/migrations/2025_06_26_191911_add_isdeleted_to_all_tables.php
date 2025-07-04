<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->after('status');
        });

        Schema::table('tour_categories', function (Blueprint $table) {
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->after('thumbnail');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->after('title');
        });

        Schema::table('album_images', function (Blueprint $table) {
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->after('caption');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->after('status');
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });

        Schema::table('tour_categories', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });

        Schema::table('album_images', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });
    }
};
