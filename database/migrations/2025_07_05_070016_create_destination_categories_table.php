<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDestinationCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('destination_categories', function (Blueprint $table) {
            $table->id('category_id'); // Tương ứng với category_id int AI PK
            $table->string('category_name', 100); // varchar(100)
            $table->string('thumbnail', 255)->nullable(); // varchar(255), có thể null
            $table->enum('is_deleted', ['active', 'inactive'])->default('active'); // enum('active', 'inactive')
            $table->timestamps(); // Tạo cột created_at và updated_at kiểu timestamp
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('destination_categories');
    }
}