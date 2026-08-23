<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectYearlyBudgetsTable extends Migration
{
    public function up()
    {
        Schema::create('project_yearly_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->date('year_start_date');
            $table->date('year_end_date');
            $table->integer('allocated_budget')->nullable();
            $table->integer('expended_budget')->nullable();
            $table->year('curr_year');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_yearly_budgets');
    }
}
