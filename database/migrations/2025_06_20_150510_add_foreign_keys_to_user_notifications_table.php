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
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->foreign(['user_id'], 'user_notifications_ibfk_1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['notification_id'], 'user_notifications_ibfk_2')->references(['notification_id'])->on('notifications')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropForeign('user_notifications_ibfk_1');
            $table->dropForeign('user_notifications_ibfk_2');
        });
    }
};
