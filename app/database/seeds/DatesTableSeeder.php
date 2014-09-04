<?php

// Composer: "fzaninotto/faker": "v1.3.0"
//use Faker\Factory as Faker;

class DatesTableSeeder extends Seeder {

    public function run()
    {
        //$faker = Faker::create();
        $month 	= 9;
        $year	=	2014;

        while($month <= 12){
        	$days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        	$i = 1;
		    while($i <= $days){
	            Date::create([
	            	'date'	=>	$year.'-'.$month.'-'.$i
	            ]);
	            ++$i;
	        }
	        ++$month;
        }
    }
}