<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');
			$table->string('username', 20)->unique();
			$table->string('password', 60);
			$table->string('fname', 100);
			$table->string('mname', 50)->nullable();
			$table->string('lname', 100);
			$table->binary('image');
			$table->integer('contact_no');
            $table->string('address')->nullable();
			$table->string('email', 60);
			$table->boolean('active');
			$table->boolean('deleted');
			$table->boolean('group');
			$table->string('remember_token', 100)->nullable();
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
	    Schema::drop('users');
	}

}
