<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTradingRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_records', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->unsignedInteger('term_id');
            $table->foreign('term_id')->references('id')->on('trading_terms');

            $table->unsignedInteger('year_id');
            $table->foreign('year_id')->references('id')->on('years');

            $table->string('datetime');
            $table->double('value',12,4);
            $table->tinyInteger('type'); //1买；0卖
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trading_records');
    }
}
