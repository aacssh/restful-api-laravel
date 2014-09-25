<?php
namespace HairConnect\Exceptions;
use Exception;

/**
 * Class ValidationException
 * @package HairConnect\Transformers
 */
class ValidationException extends Exception{
	
	/**
	 * Stores error messages
	 * @var string
	 */
	protected $errors;

	/**
	 * Constructs the exception
	 * @param string  $message  The Exception message to throw
	 * @param string  $errors   The errors to be shown
	 * @param integer $code     The Exception code
	 * @param Exception  $previous The previous exception used for the exception chaining
	 */
	public function __construct($message, $errors = '', $code = 0, Exception $previous = null)
	{
		$this->errors = $errors;
		parent::__construct($message, $code, $previous);
	}

	/**
	 * Returns the error messages stored in errors variable
	 * @return string
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}