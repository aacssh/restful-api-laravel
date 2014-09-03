<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('users')->truncate();
		DB::table('hair_style_images')->truncate();
		Eloquent::unguard();

		$this->call('UsersTableSeeder');
		$this->call('HairStyleImageTableSeeder');
	}
}
