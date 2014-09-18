<?php

class DatabaseSeeder extends Seeder {

	private $tables = [
		'appointments', 'shifts', 'dates', 'hair_style_images', 'users'
	];
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->cleanDatabase();
		Eloquent::unguard();

		$this->call('UsersTableSeeder');
		$this->call('DatesTableSeeder');
		$this->call('ShiftsTableSeeder');
		$this->call('HairStyleImageTableSeeder');
		$this->call('AppointmentsTableSeeder');
	}

	private function cleanDatabase(){
		DB::statement('SET FOREIGN_KEY_CHECKS=0');

		foreach ($this->tables as $tableName) {
			DB::table($tableName)->truncate();
		}
		DB::statement('SET FOREIGN_KEY_CHECKS=1');
	}
}
