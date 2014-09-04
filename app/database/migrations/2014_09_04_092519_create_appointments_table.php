<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppointmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('appointments', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('barber_id');
            $table->integer('client_id');
            $table->time('time');
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
	    Schema::drop('appointments');
	}

}
