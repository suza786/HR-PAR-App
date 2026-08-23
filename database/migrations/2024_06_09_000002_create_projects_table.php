<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donor_id');
            $table->string('project_code');
            $table->string('project_name');
            $table->string('short_name')->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('currency', ['EURO', 'USD', 'TAKA'])->default('EURO');
            $table->integer('total_budget')->nullable();
            $table->integer('total_revised_budget')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('donor_id')->references('id')->on('donors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
