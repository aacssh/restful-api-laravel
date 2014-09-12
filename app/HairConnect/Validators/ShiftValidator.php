<?php
namespace HairConnect\Validators;

class ShiftValidator extends Validator{

	/**
	 * [$rules description]
	 * @var array
	 */
	protected static $rules =	[
		'start_time'    =>  'required',
        'end_time' 		=>  'required',
        'time_gap' 		=>  'required',
        'date' 			=>  'required'
	];
}