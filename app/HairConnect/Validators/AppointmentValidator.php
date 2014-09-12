<?php
namespace HairConnect\Validators;

class AppointmentValidator extends Validator{
	protected static $rules = [
		'barber_id'    =>	'required',
		'time'		   =>	'required',
		'date'		   =>	'required'
	];
}