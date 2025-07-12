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
            $table->integer('booking_id', true); // Tự động tăng, khóa chính
            $table->integer('user_id')->index('user_id'); // khớp với users.id
            $table->integer('tour_id')->nullable()->index('tour_id'); 
            $table->integer('guide_id')->nullable()->index('guide_id'); 
            $table->integer('hotel_id')->nullable()->index('hotel_id');
            $table->integer('bus_route_id')->nullable()->index('bus_route_id');
            $table->integer('motorbike_id')->nullable()->index('motorbike_id');
            $table->integer('custom_tour_id')->nullable()->index('custom_tour_id');

            // Thông tin đặt chỗ
            $table->integer('quantity')->default(1);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('total_price', 12, 2);

            // Thông tin thanh toán
            $table->enum('payment_method', ['COD', 'bank_transfer', 'VNPay', 'MoMo'])->nullable();

            // Trạng thái đơn
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('cancel_reason')->nullable();

            // Xóa mềm
            $table->enum('is_deleted', ['active', 'inactive'])->default('active');

            $table->timestamps();
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