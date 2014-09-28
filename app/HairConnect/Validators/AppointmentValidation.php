<?php
namespace HairConnect\Validators;

class AppointmentValidation extends Validator{

	protected $rules = [
		'barber_id' => 'required',
		'time' => 'required',
		'date' => 'required'
	];

	protected $tokenRules = [
		'token' => 'required'
	];

	public function validateAppointmentAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->rules);
		}catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}

	public function validateToken(array $attributes){
		try{
			$this->isValid($attributes, $this->tokenRules);
		}catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}
}