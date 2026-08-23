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
        Schema::create('par_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('month', 10);
            $table->tinyInteger('week'); // value 1 to 5
            $table->date('weekStart');
            $table->date('weekEnd');
            $table->tinyInteger('working_days'); // value 1 to 6
            $table->tinyInteger('office_holidays'); // value 1 to 6
            $table->tinyInteger('total_days'); // value 1 to 7
            $table->year('year');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('par_calendars');
    }
};
