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
        Schema::create('salary_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('emp_id')->constrained('employees', 'emp_id');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->decimal('allocation_ratio', 5, 2); // e.g., 0.50 for 50%
            $table->date('start_month'); // 1-12
            $table->date('end_month'); // 1-12
            $table->year('curr_year');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_allocations');
    }
};
