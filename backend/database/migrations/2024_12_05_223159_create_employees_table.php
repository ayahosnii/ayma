<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign Key to Users Table
            $table->date('hire_date')->nullable(); // Hire Date
            $table->string('job_title')->nullable(); // Job Title
            $table->string('department')->nullable(); // Department
            $table->decimal('salary', 10, 2)->nullable(); // Salary
            $table->timestamps(); // Created_At & Updated_At
            $table->softDeletes(); // Deleted_At for Soft Deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
