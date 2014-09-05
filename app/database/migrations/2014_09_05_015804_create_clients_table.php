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
            $table->integer('login_id');
            $table->string('fname', 100);
			$table->string('lname', 100);
			$table->binary('image');
			$table->bigInteger('contact_no');
            $table->string('address')->nullable();
			$table->string('email', 60);
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
