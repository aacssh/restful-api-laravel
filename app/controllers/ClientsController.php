<?php
use \HairConnect\Transformers\ClientsTransformer;

class ClientsController extends \BaseController {

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
	 * [__construct description]
	 * @param ClientsTransformer $clientsTransformer [description]
	 */
	function __construct(ClientsTransformer $clientsTransformer, APIController $apiController){
		$this->clientsTransformer 	= 	$clientsTransformer;
		$this->apiController 		=	$apiController;
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
		$client 	=	User::findByUsernameOrFail($username)->client;
		if($client->count()){
			return $this->apiController->respond([
				'details'	=> 	$this->clientsTransformer->transform($client)
			]);
		}

		return $this->apiController->respondNotFound('Client cannot be found or the account is deactivated.');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $username
	 * @return Response
	 */
	public function update($username)
	{
		$validation = Validator::make(Input::all(), [
			'fname'			=> 	'required|Alpha',
			'lname'			=>	'required|Alpha',
			'contact_no'	=>	'required|numeric',
			'address'		=>	'required',
			'email'			=>	'required|email'
		]);

		if(!$validation->fails()){
			$user 	 	=	User::findByUsernameOrFail($username);
			$client 	=	$user->client;

			if($client->count()){
				$client->fname 		= Input::get('fname');
				$client->lname 		= Input::get('lname');
				$client->contact_no = Input::get('contact_no');
				$client->address 	=	Input::get('address');
				$client->save();

				$user->email 		=	Input::get('email');
				$user->save();
			
				return $this->apiController->respond([
					'message'	=>	'Profile info have been updated.',
					'data'		=>	$this->clientsTransformer->transform($client)
				]);
			}
		}
		return $this->apiController->respondNotFound($validation->messages());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string 	$username
	 * @return Response
	 */
	public function destroy($username)
	{
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
}