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
		DB::table('barbers')->truncate();
		DB::table('clients')->truncate();
		DB::table('dates')->truncate();
		DB::table('shifts')->truncate();
		DB::table('hair_style_images')->truncate();
		DB::table('appointments')->truncate();
		Eloquent::unguard();

		$this->call('UsersTableSeeder');
		$this->call('BarbersTableSeeder');
		$this->call('ClientsTableSeeder');
		$this->call('DatesTableSeeder');
		$this->call('ShiftsTableSeeder');
		$this->call('HairStyleImageTableSeeder');
		$this->call('AppointmentsTableSeeder');
	}
}
