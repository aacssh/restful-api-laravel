<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class HairStyleImageTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();
        $barbersId = User::where('group', '=', 0)->lists('id');

        foreach(range(1, 10) as $index)
        {
            DB::table('hair_style_images')->insertGetId([
            	'barber_id' 	=> 	$faker->randomElement($barbersId),
                'image'     	=>	$faker->imageUrl(300, 200),
                'image_title'	=>	$faker->sentence()
            ]);
        }
    }
}