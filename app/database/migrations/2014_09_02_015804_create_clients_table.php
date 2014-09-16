<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('clients', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('fname', 100);
			$table->string('lname', 100);
			$table->binary('image');
			$table->bigInteger('contact_no');
			$table->integer('zip');
			$table->string('address');
            //$table->integer('address_id')->unsigned();
            //$table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
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
	    Schema::drop('clients');
	}

}
