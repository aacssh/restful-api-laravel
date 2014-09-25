<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class AppointmentsTableSeeder extends Seeder {

  public function run(){
    $Faker  =  Faker::create();
    $datesId  =  DB::table('dates')->lists('id');

    $barberIds  =  User::ofType('barber')->get();
    foreach((array)$barberIds as $barbers) {
      foreach ($barbers as $barber) {
        $barber_id[] = $barber->id;
      }
    }

    $clientIds  =  User::ofType('client')->get();
    foreach((array)$clientIds as $clients) {
      foreach ($clients as $client) {
        $client_id[] = $client->id;
      }
    }

    foreach(range(1, 30) as $index){
      Appointment::create([
      	'barber_id'  =>  $faker->randomElement($barber_id),
      	'client_id'  =>  $faker->randomElement($client_id),
      	'time'  =>  $faker->time($format = 'H:i:s', $max = 'now'),
      	'date_id'  =>  $faker->randomElement($datesId)
      ]);
    }
  }
}