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
		DB::table('dates')->truncate();
		DB::table('appointments')->truncate();
		Eloquent::unguard();

		$this->call('UsersTableSeeder');
		$this->call('HairStyleImageTableSeeder');
		$this->call('DatesTableSeeder');
		$this->call('AppointmentsTableSeeder');
	}
}
