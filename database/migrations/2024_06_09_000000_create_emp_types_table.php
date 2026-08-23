<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpTypesTable extends Migration
{
    public function up()
    {
        Schema::create('emp_types', function (Blueprint $table) {
            $table->id();
            $table->string('emp_type');
            $table->string('type_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emp_types');
    }
}
