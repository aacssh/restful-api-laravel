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
            $table->string('email', 60);
			$table->string('username', 20)->unique();
			$table->string('password', 60);
			$table->string('password_temp', 60);
			$table->string('code', 60);
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
