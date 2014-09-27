<?php
namespace HairConnect\Validators;

class TokenValidation extends Validator{
	protected $rules = [
		'token' => 'required'
	];

	public function validateToken(array $token){
		try{
			$this->isValid($token, $this->rules);
		}catch(ValidationException $e){
			throw new ValidationException($this->getErrors());
		}
	}
}