<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class HairStyleImageTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();
        $barberIds  =   User::ofType('barber')->get();
        foreach ((array)$barberIds as $barbers) {
            foreach ($barbers as $barber) {
                $barber_id[] = $barber->id;
            }
        }

        foreach(range(1, 10) as $index)
        {
            HairStyleImage::create([
            	'user_id' 	=> 	$faker->unique()->randomElement($barber_id),
                'image'     	=>	$faker->imageUrl(300, 200),
                'image_title'	=>	$faker->sentence()
            ]);
        }
    }
}