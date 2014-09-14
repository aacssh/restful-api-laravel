<?php
namespace HairConnect\Validators;

class LoginValidator extends Validator{
	public static $rules = [
		'email' 			=> 'required|max:50|email',
		'password' 			=> 'required|min:8'
	];
}