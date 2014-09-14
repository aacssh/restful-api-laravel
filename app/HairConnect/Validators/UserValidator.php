<?php
namespace HairConnect\Validators;

class UserValidator extends Validator{
	public static $rules = [
		'name'              =>  'required',
        'username' 			=>  'required|max:20|min:2|unique:users',
        'password' 			=>  'required|min:6',
        'confirm_password' 	=>  'required|same:password',
        'email' 			=>  'required|max:60|email|unique:users',
        'type'				=>  'required'
	];
}