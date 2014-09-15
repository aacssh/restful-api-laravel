<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        foreach(range(1, 20) as $index)
        {
            User::create([
                'email'     =>  $faker->email,
                'username'  =>  $faker->userName,
                'password'  =>  Hash::make('software')
            ]);
        }
    }
}