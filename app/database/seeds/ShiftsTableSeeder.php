<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ShiftsTableSeeder extends Seeder {

  public function run(){
    $faker  =  Faker::create();
    $barberIds  =  User::ofType('barber')->get();
    foreach((array)$barberIds as $barbers) {
      foreach ($barbers as $barber) {
        $barber_id[] = $barber->id;
      }
    }
    $dateIds  =  Date::lists('id');

    foreach(range(1, 10) as $index){
      Shift::create([
      	'user_id'  =>  $faker->randomElement($barber_id),
      	'start_time'  =>  $faker->time(),
      	'end_time'  =>	$faker->time(),
      	'time_gap'  =>	20,
        'date_id'  =>  $faker->randomElement($dateIds),
      	'deleted'  =>	$faker->boolean
      ]);
    }
  }
}