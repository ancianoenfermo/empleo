<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string("datePosted",100);
            $table->string("title",100);
            $table->text("excerpt")->nullable();
            $table->string("jobUrl");
            $table->string("jobSource");
            $table->string("logo")->nullable();
            $table->string("contract", 50)->nullable();
            $table->string("workingDay", 50)->nullable();
            $table->string("experience", 50)->nullable();
            $table->string("vacancies", 50)->nullable();
            $table->string("salary", 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
