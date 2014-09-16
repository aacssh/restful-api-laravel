<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHairStyleImageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('hair_style_images', function(Blueprint $table) {
            $table->increments('id');
			$table->integer('barber_id')->unsigned();
            $table->foreign('barber_id')->references('id')->on('barbers')->onDelete('cascade');
			$table->binary('image');
			$table->string('image_title');
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
	    Schema::drop('hair_style_images');
	}

}
