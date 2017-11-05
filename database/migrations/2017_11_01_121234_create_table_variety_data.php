<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableVarietyData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variety_data', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('variety_id');
            $table->foreign('variety_id')->references('id')->on('varieties');


            $table->date('date');
            $table->double('closing',15,2);
            $table->double('opened',15,2);
            $table->double('highest',15,2);
            $table->double('minimum',15,2);
            $table->integer('deal');
            $table->integer('positions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variety_data');
    }
}
