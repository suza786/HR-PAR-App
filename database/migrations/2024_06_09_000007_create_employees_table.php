<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('emp_id')->unique();
            $table->unsignedBigInteger('emp_type_id');
            $table->unsignedBigInteger('role_id');
            $table->string('name');
            $table->unsignedBigInteger('supervisor_id');
            $table->unsignedBigInteger('repo_supervisor_id')->nullable();
            $table->string('password');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('email')->unique();
            $table->date('birth_date')->nullable();
            $table->date('join_date')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('position')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->string('mobile')->nullable();
            $table->enum('vr_type', [ 0 => 'VR Submission Authority', 1 => 'VR Assign Authority', 2 => 'VR Approve Authority', 3 => 'Both Assign & Approve Authority' ]);
            $table->boolean('user_status')->default(true);
            $table->date('separation_date')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('emp_type_id')->references('id')->on('employee_types')->onDelete('cascade');
            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            // $table->foreign('supervisor_id')->references('emp_id')->on('employees')->onDelete('set null');
            // $table->foreign('repo_supervisor_id')->references('emp_id')->on('employees')->onDelete('set null');
            // $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
            // $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            // $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
