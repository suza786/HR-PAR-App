<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('service_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->enum('transition_type', ['New Hire', 'Resignation', 'Promotion', 'Transfer', 'End of Contact', 'Re-join'])->default('New Hire');
            $table->string('designation')->nullable();
            $table->string('order_num')->nullable();
            $table->integer('order_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_histories');
    }
}
