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
            $table->string('email', 60)->unique();
			$table->string('username', 20)->unique();
			$table->string('password', 60);
			$table->string('password_temp', 60);
			$table->string('access_token', 60)->unique()->nullable();
			$table->boolean('online');
			$table->string('fname', 100);
			$table->string('lname', 100);
			$table->binary('image');
			$table->string('shop_name', 100);
			$table->bigInteger('contact_no');
			$table->integer('zip');
			$table->string('address');
			$table->string('type', 7);
			$table->string('code', 60);
			$table->string('remember_token', 100)->nullable();
			$table->boolean('deactivated');
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
