<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBarbersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('barbers', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('fname', 100);
			$table->string('lname', 100);
			$table->binary('image');
			$table->bigInteger('contact_no');
            $table->string('address')->nullable();
			$table->boolean('active');
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
	    Schema::drop('barbers');
	}

}
