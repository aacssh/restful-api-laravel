<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ClientsTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();
        $user  = User::lists('id');
        foreach(range(1, 10) as $index)
        {
            Client::create([
            	'login_id'	=>	$faker->unique()->randomElement($user),
            	'fname'     =>  $faker->firstName,
                'lname'     =>  $faker->lastName,
                'image'     =>  $faker->imageUrl(300, 200),
                'contact_no'=>  $faker->phoneNumber(),
                'address'   =>  $faker->address,
                'active'    =>  $faker->boolean(),
                'deleted'   =>  $faker->boolean()
            ]);
        }
    }

}