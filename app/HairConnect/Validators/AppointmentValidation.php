<?php
namespace HairConnect\Validators;

class AppointmentValidation extends Validator{

	protected $rules = [
		'barber_id' => 'required',
		'time' => 'required',
		'date' => 'required'
	];

	public function validateAppointmentAttributes(array $attributes){
		try{
			$this->isValid($attributes, $this->rules);
		}catch(ValidationException $e){
			throw new ValidationException($this->getErrors());
		}
	}
}