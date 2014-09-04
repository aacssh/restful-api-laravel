<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class AppointmentsTableSeeder extends Seeder {

    public function run()
    {
        $faker 		= Faker::create();
        $barbersId 	= User::where('group', '=', 0)->lists('id');
        $clientsId 	= User::where('group', '=', 1)->lists('id');
        $datesId 	= DB::table('dates')->lists('id');

        foreach(range(1, 30) as $index)
        {
            Appointment::create([
            	'barber_id'	=>	$faker->randomElement($barbersId),
            	'client_id'	=>	$faker->randomElement($clientsId),
            	'time'		=>	$faker->time($format = 'H:i:s', $max = 'now'),
            	'date_id'	=>	$faker->randomElement($datesId)
            ]);
        }
    }

}