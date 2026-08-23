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
        Schema::create('monthly_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('par_calendar_id')->constrained('par_calendars')->onDelete('cascade');
            $table->string('emp_id')->constrained('employees', 'emp_id');
            $table->unsignedTinyInteger('leave_hrs');
            $table->unsignedTinyInteger('working_hrs');
            $table->string('note')->nullable();
            $table->enum('status', ['pending', 'requested', 'approved', 'rejected'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_activities');
    }
};
