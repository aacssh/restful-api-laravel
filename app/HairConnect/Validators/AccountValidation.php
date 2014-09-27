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
		'confirm_password' => 'required|same:new_password',
		'username' => 'required'
	];

	protected $registerRules = [
		'name' => 'required',
    'username' => 'required|max:20|min:2|unique:users',
    'password' => 'required|min:6',
    'confirm_password' => 'required|same:password',
    'email' => 'required|max:60|email|unique:users',
    'type' => 'required'
	];

	protected $tokenUsernameRules = [
		'username' => 'required',
		'token' => 'required'
	];

	public function validateLoginAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->loginRules);
		}catch(ValidationException $e){
			throw new ValidationException($this->getErrors());
		}
	}

	public function validatePasswordRecoveryAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->passwordRecoveryRules);
		}catch(ValidationException $e){
			throw new ValidationException($this->getErrors());
		}
	}

	public function validatePasswordUpdateAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->passwordUpdateRules);
		}catch(ValidationException $e){
			throw new ValidationException($this->getErrors());
		}
	}

	public function validateRegisterAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->registerRules);
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