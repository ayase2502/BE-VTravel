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
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->integer('user_notification_id', true);
            $table->integer('user_id')->index('user_id');
            $table->integer('notification_id')->index('notification_id');
            $table->enum('status', ['unread', 'read'])->nullable()->default('unread');
            $table->timestamp('read_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
