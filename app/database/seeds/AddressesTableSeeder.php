<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class AddressesTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        foreach(range(1, 10) as $index)
        {
            Address::create([
            	'zip' => $faker->postcode,
            	'address' => $faker->city.', '.$faker->state
            ]);
        }
    }

}