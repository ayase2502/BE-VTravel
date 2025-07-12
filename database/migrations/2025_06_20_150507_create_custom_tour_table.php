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
        // Schema::create('custom_tour', function (Blueprint $table) {
        //     $table->integer('custom_tour_id', true);
        //     $table->integer('user_id')->nullable()->index('user_id');
        //     $table->decimal('total_price', 12)->nullable();
        //     $table->enum('status', ['draft', 'confirmed', 'cancelled', 'completed'])->nullable()->default('draft');
        //     $table->timestamp('created_at')->nullable()->useCurrent();
        //     $table->enum('is_deleted', ['active', 'inactive'])->default('active')->comment('active = hoạt động, inactive = không hoạt động (ẩn)');
        // });
        Schema::create('custom_tours', function (Blueprint $table) {
            $table->integer('custom_tour_id',true);
            $table->integer('user_id');
            $table->string('destination');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('num_people');
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('is_deleted', ['active', 'inactive'])->default('active')->comment('active = hoạt động, inactive = không hoạt động (ẩn)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_tours');
    }
};
