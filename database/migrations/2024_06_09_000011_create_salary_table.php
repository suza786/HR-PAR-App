<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryTable extends Migration
{
    public function up()
    {
        Schema::create('salary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->double('basic')->nullable();
            $table->double('perquisites')->nullable();
            $table->double('pf_organization')->nullable();
            $table->double('gratuity')->nullable();
            $table->double('bonus')->nullable();
            $table->double('income_tax')->nullable();
            $table->date('efective_date')->nullable();
            $table->year('salary_year')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary');
    }
}
