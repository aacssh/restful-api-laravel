<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        foreach(range(1, 10) as $index)
        {
            User::create([
                'username'  =>  $faker->userName,
                'password'  =>  Hash::make('software'),
                'fname'     =>  $faker->firstName,
                'lname'     =>  $faker->lastName,
                'image'     =>  $faker->imageUrl(300, 200),
                'contact_no'=>  $faker->phoneNumber(),
                'address'   =>  $faker->address,
                'email'     =>  $faker->email,
                'active'    =>  $faker->boolean(),
                'deleted'   =>  $faker->boolean(),
                'group'     =>  $faker->boolean()
            ]);
        }
    }
}