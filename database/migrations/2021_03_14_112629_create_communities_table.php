<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("job_id")->nullable();
            $table->foreign("job_id")->references("id")->on("jobs");
            $table->unsignedBigInteger("province_id")->nullable();
            $table->foreign("province_id")->references("id")->on("Provinces");
            $table->string("name", 50)->unique();
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
        Schema::dropIfExists('communities');
    }
}
