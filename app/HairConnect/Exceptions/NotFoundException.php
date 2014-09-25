<?php
namespace HairConnect\Exceptions;
use Exception;

class NotFoundException extends Exception{
	/**
	 * Constructs the exception
	 * @param string  $message  The Exception message to throw
	 * @param integer $code     The Exception code
	 * @param Exception  $previous The previous exception used for the exception chaining
	 */
	public function __construct($message = 'Given credentials didn\'t matched with any users.', $code = 0, Exception $previous = null){
		parent::__construct($message, $code, $previous);
	}
}