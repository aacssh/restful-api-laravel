<?php
use Illuminate\Http\Response as IlluminateResponse;

class APIResponse extends BaseController{
	
	/**
	 * Contains the status code for each request
	 * @var integer
	 */
	protected $statusCode = IlluminateResponse::HTTP_OK;
	
	/**
	 * Sets the status code for each request
	 * @param integer $statusCode
	 */
	public function setStatusCode($statusCode)
	{	
		$this->statusCode = $statusCode;
		return $this;
	}

	/**
	 * Returns the current status code
	 * @return integer status code
	 */
	public function getStatusCode(){
		return $this->statusCode;
	}

	/**
	 * Returns an error message along with not found status code
	 * @param  string $message
	 * @return mixed
	 */
	public function respondNotFound($message = 'Not Found'){
		return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
	}

	/**
	 * Returns a json formatted data through Response class
	 * @param  mixed $data
	 * @param  array $headers
	 * @return mixed
	 */
	public function respond($data, $headers = []){
		return Response::json($data, $this->getStatusCode(), $headers);
	}

	/**
	 * This returns a success message along with the appropriate status code
	 * @param  string $message
	 * @return mixed
	 */
	public function respondSuccess($message = 'Success'){
		return $this->respond([
			'success' => [
				'message' => $message,
				'status_code' => $this->getStatusCode()
			]
		]);
	}

	/**
	 * This returns a success message and user details along with the appropriate status code
	 * @param  string $message
	 * @return mixed
	 */
	public function respondSuccessWithDetails($message = 'Success', $details = ''){
		return $this->setStatusCode(IlluminateResponse::HTTP_OK)->respond([
			'success' =>[
				'message' => $message,
				'status_code' => $this->getStatusCode(),
				'data'	=>	$details
			]
		]);
	}

	/**
	 * This returns a success message and user details along with the appropriate status code
	 * @param  string $message
	 * @return mixed
	 */
	public function respondNoContent($message = 'No content'){
		return $this->setStatusCode(IlluminateResponse::HTTP_NO_CONTENT)->respondSuccessWithDetails($message);
	}	

	/**
	 * This returns an error message along with the status code
	 * @param  string $messsage
	 * @return mixed
	 */
	public function respondWithError($message){
		return $this->respond([
			'errors' => [
				'message' => $message,
				'status_code' => $this->getStatusCode()
			]
		]);
	}

	/**
	 * This returns Internal Server Error message along with the status code
	 * @param  string $message
	 * @return mixed
	 */
	public function respondInternalError($message = 'Internal Error'){
		return $this->setStatusCode(IlluminateResponse::INTERAL_SERVER_ERROR)->respondWithError($message);
	}

	/**
	 * This returns a success message along with the appropriate status code
	 * @param  string $message
	 * @return mixed
	 */
	public function respondCreated($message = 'Successfully Created'){
		return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)->respondSuccess($message);
	}

	/**
	 * This returns a validation failed message along with the status code
	 * @param  string $message
	 * @return mixed
	 */
	public function respondInvalidParameters($message = 'Invalid Parameters'){
		return $this->setStatusCode(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
	}

	/**
	 * This returns, along with the status code, an error message that requested data cannot be saved 
	 * @param  string $message [description]
	 * @return [type]          [description]
	 */
	public function respondNotSaved($message = 'Cannot be saved'){
		return $this->setStatusCode(304)->respondWithError($message);
	}
}