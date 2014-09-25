<?php
namespace HairConnect\Validators;
use HairConnect\Exceptions\ValidationException;
use Validator as V;

/**
 * Class Validator
 * @package HairConnect\Transformers
 */
class Validator{

	/**
	 * Checks if data are valid or not.
	 * @param  array   $attributes All input data
	 * @param  array   $rules      Valdation rules
	 * @return boolean
	 */
	public function isValid(array $attributes, $rules)
	{
		$v = V::make($attributes, $rules);

		if($v->fails()){
			$this->errors = $v->messages();
			throw new ValidationException('Email or password does not match.');
		}
		return true;
	}

	/**
	 * Returns the error messages stored in errors variable
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}