<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShiftsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('shifts', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('barber_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('time_gap');
            $table->integer('date_id');
            $table->boolean('deleted');
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
	    Schema::drop('shifts');
	}

}
