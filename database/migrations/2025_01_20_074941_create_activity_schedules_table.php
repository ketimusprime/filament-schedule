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
        Schema::create('activity_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('activity_date');
            $table->time('activity_time');
            $table->string('No_OP')->nullable();
            $table->enum('order', ['kerjasama', 'walkin', 'online', 'reserve'])->default('walkin');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('subcategory_id');
            // $table->unsignedBigInteger('product_id');
            $table->enum('status', ['pending', 'done', 'cancel', 'confirmed'])->default('pending');
            $table->unsignedBigInteger('user_id');
            $table->string('package')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('subcategory_id')->references('id')->on('sub_categories')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_schedules');
    }
};
