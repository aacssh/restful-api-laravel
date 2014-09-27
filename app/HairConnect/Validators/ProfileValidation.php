<?php
namespace HairConnect\Validators;

class ProfileValidation extends Validator{

	/**
	 * Validation rules for user profile
	 * @var array
	 */
	protected $profileRules = [
		'fname'  =>  'required|Alpha',
		'lname'  =>  'required|Alpha',
		'contact_no'  =>  'required|numeric',
		'city'  =>  'required',
		'state'  =>  'required',
		'email'  =>  'required|email',
		'token' => 'required'
	];

	protected $tokenUsernameRules = [
		'username' => 'required',
		'token' => 'required'
	];

	public function validateProfileAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->profileRules);
		}catch(ValidationException $e){
			throw new ValidationException($this->getErrors());
		}
	}

	public function validateTokenAndUsername(array $attributes){
		try{
			$this->isValid($attributes, $this->tokenUsernameRules);
		}catch(ValidationException $e){
			throw new ValidationException($this->getErrors());
		}
	}
}