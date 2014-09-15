<?php
namespace HairConnect\Validators;
use Validator as V;

class Validator{
	protected $errors;

	public function isValid(array $attributes, array $rules)
	{
		$v = V::make($attributes, $rules);

		if($v->fails()){
			$this->errors = $v->messages();
			return false;
		}
		return true;
	}

	public function getErrors()
	{
		return $this->errors;
	}
}