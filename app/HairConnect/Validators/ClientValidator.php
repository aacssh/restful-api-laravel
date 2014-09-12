<?php
namespace HairConnect\Validators;

class ClientValidator extends Validator{

	/**
	 * [$rules description]
	 * @var array
	 */
	protected static $rules = [
		'fname'			=> 	'required|Alpha',
		'lname'			=>	'required|Alpha',
		'contact_no'	=>	'required|numeric',
		'address'		=>	'required',
		'email'			=>	'required|email'	
	];
}