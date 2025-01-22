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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_schedule_id');
            $table->unsignedBigInteger('user_id');
            $table->string('work_as');
            $table->enum('work_day', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'])->default('senin');
            $table->string('work_start');
            $table->string('work_end');
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
        Schema::dropIfExists('employees');
    }
};
