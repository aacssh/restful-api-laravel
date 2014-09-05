<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ShiftsTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();
        $barberIds	=	Barber::lists('id');

        foreach(range(1, 10) as $index)
        {
            Shift::create([
            	'barber_id'		=>	$faker->unique()->randomElement($barberIds),
            	'start_time'	=>	$faker->time(),
            	'end_time'		=>	$faker->time(),
            	'time_gap'		=>	20,
            	'deleted'		=>	$faker->boolean
            ]);
        }
    }

}