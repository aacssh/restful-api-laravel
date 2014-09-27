<?php
namespace HairConnect\Validators;

class ShiftValidation extends Validator{

	/**
	 * Validation rules for shift
	 * @var array
	 */
	protected $rules =	[
		'start_time' => 'required',
    'end_time' => 'required',
    'time_gap' => 'required',
    'date' => 'required'
	];

	protected $tokenRules = [
		'token' => 'required',
		'username' => 'required'
	];
	
	public function validateShiftAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->rules);
		}catch(ValidationException $e){
			throw new ValidationException($this->getErrors());
		}
	}

	public function validateTokenAndUsername(array $attributes){
		try{
			$this->isValid($attributes, $this->tokenRules);
		}catch(ValidationException $e){
			throw new ValidationException($this->getErrors());
		}
	}
}