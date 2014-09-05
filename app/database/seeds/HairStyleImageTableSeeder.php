<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class HairStyleImageTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();
        $barbersId = Barber::lists('id');

        foreach(range(1, 10) as $index)
        {
            HairStyleImages::create([
            	'barber_id' 	=> 	$faker->unique()->randomElement($barbersId),
                'image'     	=>	$faker->imageUrl(300, 200),
                'image_title'	=>	$faker->sentence()
            ]);
        }
    }
}