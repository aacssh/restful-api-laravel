<?php
use Illuminate\Http\Response as IlluminateResponse;

class APIController extends BaseController{
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
	 * Returns the error message along with not found status code
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
	 * Returns a json formatted data through respond() function
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
	 * @param  string $message
	 * @return mixed
	 */
	public function respondInternalError($message = 'Internal Error'){
		return $this->setStatusCode(IlluminateResponse::INTERAL_SERVER_ERROR)->respondWithError($message);
	}

	public function respondCreated($message = 'Successfully Created'){
		return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)->respond([
			'success' =>[
				'message' => $message,
				'status_code' => $this->getStatusCode()
			]
		]);
	}

	public function respondInvalidParameters($message = 'Invalid Parameters'){
		return $this->setStatusCode(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
	}

	public function respondNotSaved($message = 'Cannot be saved'){
		return $this->setStatusCode(304)->respondWithError($message);
	}
}