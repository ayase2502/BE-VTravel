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
        Schema::create('payments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('booking_id')->index('booking_id');
            $table->integer('payment_method_id')->index('payment_method_id');
            $table->decimal('amount', 12);
            $table->enum('status', ['pending', 'completed', 'failed'])->nullable()->default('pending');
            $table->string('transaction_code', 100)->nullable()->unique('transaction_code');
            $table->timestamp('paid_at')->nullable();
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->comment('active = hoạt động, inactive = không hoạt động (ẩn)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
