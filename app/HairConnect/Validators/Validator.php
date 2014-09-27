<?php
namespace HairConnect\Validators;
use HairConnect\Exceptions\ValidationException;
use Validator as V;

/**
 * Class Validator
 * @package HairConnect\Transformers
 */
class Validator{

	protected $errors;

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
			foreach ($v->messages()->all() as $message){
				$this->errors .= ' '.$message;
			}
			throw new ValidationException($this->getErrors());
		}
	}

	/**
	 * Returns the error messages stored in errors variable
	 * @return array
	 */
	public function getErrors()
	{
		return trim($this->errors);
	}
}