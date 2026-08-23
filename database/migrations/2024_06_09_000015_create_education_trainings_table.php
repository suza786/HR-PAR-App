<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationTrainingsTable extends Migration
{
    public function up()
    {
        Schema::create('education_trainings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('degree')->nullable();
            $table->string('board_university_institute')->nullable();
            $table->string('session_duration')->nullable();
            $table->string('result_marks')->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('education_trainings');
    }
}
