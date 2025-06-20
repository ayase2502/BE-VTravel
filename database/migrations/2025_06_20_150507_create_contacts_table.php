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
        Schema::create('contacts', function (Blueprint $table) {
            $table->integer('contact_id', true);
            $table->string('name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('message')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->enum('status', ['new', 'processed'])->nullable()->default('new');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
