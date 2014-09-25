<?php
namespace HairConnect\Validators;

class AccountValidation extends Validator{
	
	protected $loginRules = [
		'email'	=> 'required|max:50|email',
		'password' => 'required|min:8'
	];

	protected $passwordRecoveryRules = [
		'email' => 'required|email'
	];

	protected $passwordUpdateRules = [
		'old_password' => 'required',
		'new_password' => 'required|min:6',
		'confirm_password' => 'required|same:new_password'
	];

	protected $registerRules = [
		'name' => 'required',
    'username' => 'required|max:20|min:2|unique:users',
    'password' => 'required|min:6',
    'confirm_password' => 'required|same:password',
    'email' => 'required|max:60|email|unique:users',
    'type' => 'required'
	];

	public function validateLoginAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->loginRules);
			return true;
		}catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}

	public function validatePasswordRecoveryAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->passwordRecoveryRules);
			return true;
		}catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}

	public function validatePasswordUpdateAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->passwordUpdateRules);
			return true;
		}catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}

	public function validateRegisterAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->registerRules);
			return true;
		}catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}
}