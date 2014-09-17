<?php
use \HairConnect\Transformers\ClientsTransformer;
use \HairConnect\Services\ClientService;
use \HairConnect\Validators\ValidationException;

class ClientsController extends TokensController {

	/**
	 * [$clientsTransformer description]
	 * @var [type]
	 */
	protected $clientsTransformer;

	/**
	 * [$apiController description]
	 * @var [type]
	 */
	protected $apiController;

	/**
	 * [$clientService description]
	 * @var [type]
	 */
	protected $clientService;	

	/**
	 * [__construct description]
	 * @param ClientsTransformer $clientsTransformer [description]
	 */
	function __construct(ClientsTransformer $clientsTransformer, APIController $apiController, ClientService $clientService){
		$this->clientsTransformer 	= 	$clientsTransformer;
		$this->apiController 		=	$apiController;
		$this->clientService 		=	$clientService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($barber = null)
	{
		$users = Client::all();
        return $this->apiController->respond([
            'data' 	=>	$this->clientsTransformer->transformCollection($users->all())
        ]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($username)
	{
		if($this->checkToken(Input::get('token'), $username)){
			$client = User::findByUsernameOrFail($username)->client;

			if($client->count()){
				return $this->apiController->respond([
					'details'	=> 	$this->clientsTransformer->transform($client)
				]);
			}
			return $this->apiController->respondNotFound('Client cannot be found or the account is deactivated.');
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token'
            ]
        ]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $username
	 * @return Response
	 */
	public function update($username)
	{
		if($this->checkToken(Input::get('token'), $username)){
			try{
				$client = $this->clientService->update($username, Input::all());

				return $this->apiController->respond([
					'message'	=>	'Profile info have been updated.',
					'data'		=>	$this->clientsTransformer->transform($client)
				]);
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getErrors());
			}
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token'
            ]
        ]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string 	$username
	 * @return Response
	 */
	public function destroy($username)
	{
		if($this->checkToken(Input::get('token'), $username)){
			$user 	   =	User::findByUsernameOrFail($username);
			$client    =	$user->client;
			if($client->count()){
				$client->active		=	0;
				$client->deleted	=	0;
				$client->save();

				return $this->apiController->respond([
					'message' 	=> 'Account has been successfully deactivated.',
					'activate'	=>	(bool)$client->deleted
				]);
			}
			return $this->apiController->respondNotFound('Client not registered or deactivated.');
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token'
            ]
        ]);
	}
}