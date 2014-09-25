<?php
namespace HairConnect\Validators;

class ShifValidation extends Validator{

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

	public function validateShiftAttributes(array $attributes){
		$this->isValid($attributes, $this->rules);
		return true;
	}
}