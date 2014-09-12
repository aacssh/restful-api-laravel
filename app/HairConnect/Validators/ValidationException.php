<?php
namespace HairConnect\Validators;
use Exception;

class ValidationException extends Exception{
	/**
	 * [$errors description]
	 * @var [type]
	 */
	protected $errors;

	/**
	 * 
	 * @param [type]  $message  [description]
	 * @param [type]  $errors   [description]
	 * @param integer $code     [description]
	 * @param [type]  $previous [description]
	 */
	public function __construct($message, $errors, $code = 0, Exception $previous = null)
	{
		$this->errors = $errors;
		parent::__construct($message, $code, $previous);
	}

	public function getErrors()
	{
		return $this->errors;
	}
}