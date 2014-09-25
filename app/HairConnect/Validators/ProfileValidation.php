<?php
namespace HairConnect\Validators;

class ProfileValidation extends Validator{

	/**
	 * Validation rules for user profile
	 * @var array
	 */
	protected $rules = [
		'fname'  =>  'required|Alpha',
		'lname'  =>  'required|Alpha',
		'contact_no'  =>  'required|numeric',
		'city'  =>  'required',
		'state'  =>  'required',
		'email'  =>  'required|email'
	];

	public function validateProfileAttributes(array $attributes){
		$this->isValid($attributes, $this->rules);
		return true;
	}
}