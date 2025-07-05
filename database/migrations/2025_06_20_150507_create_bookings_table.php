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
        Schema::create('bookings', function (Blueprint $table) {
            $table->integer('booking_id', true);
            $table->integer('user_id')->nullable()->index('user_id');
            $table->enum('booking_type', ['tour', 'combo', 'hotel', 'transport', 'motorbike', 'guide', 'bus'])->nullable();
            $table->integer('related_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('quantity')->nullable()->default(1);
            $table->decimal('total_price', 12)->nullable();
            $table->enum('payment_method', ['COD', 'bank_transfer', 'VNPay', 'MoMo'])->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->nullable()->default('pending');
            $table->text('cancel_reason')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->comment('active = hoạt động, inactive = không hoạt động (ẩn)');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
