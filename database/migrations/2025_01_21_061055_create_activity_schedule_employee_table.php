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
        Schema::create('activity_schedule_employee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_schedule_id');
            $table->unsignedBigInteger('user_id');
            $table->string('crew');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('activity_schedule_id')->references('id')->on('activity_schedules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_schedule_employee');
    }
};
