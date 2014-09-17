<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();
        $type = ['barber', 'client'];

        foreach(range(1, 20) as $index)
        {
            User::create([
                'email'         =>  $faker->email,
                'username'      =>  $faker->userName,
                'password'      =>  Hash::make('software'),
                'online'        =>  $faker->boolean(),
                'fname'         =>  $faker->firstName,
                'lname'         =>  $faker->lastName,
                'image'         =>  $faker->imageUrl(300, 200),
                'shop_name'     =>  $faker->name,
                'contact_no'    =>  $faker->phoneNumber,
                'zip'           =>  $faker->postcode,
                'address'       =>  $faker->city.', '.$faker->state,
                'type'          =>  $faker->randomElement($type),
                'deactivated'   =>  $faker->boolean()
            ]);
        }
    }
}