<?php
use \HairConnect\Transformers\ClientsTransformer;

class ClientsController extends \BaseController {

	/**
	 * [$clientsTransformer description]
	 * @var [type]
	 */
	protected $clientsTransformer;

	/**
	 * [__construct description]
	 * @param ClientsTransformer $clientsTransformer [description]
	 */
	function __construct(ClientsTransformer $clientsTransformer){
		$this->clientsTransformer 	= $clientsTransformer;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = Client::all();
        return Response::json([
            'data' 					=> $this->clientsTransformer->transformCollection($users->all())
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
		$login_id					=	User::whereUsername($username)->get();
		if($login_id->count()){
			$client 				= 	Client::where('login_id', '=', $login_id->first()->id);

			if($client->count()){
				return Response::json([
					'details'		=> 	$this->clientsTransformer->transform($client->first())
				]);
			}
		}		

		return Response::json([
			'errors' => [
				'message'			=>	'Client cannot be found or the account is deactivated.'
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
		$validation = Validator::make(Input::all(), [
			'fname'					=> 	'required|Alpha',
			'lname'					=>	'required|Alpha',
			'contact_no'			=>	'required|numeric',
			'address'				=>	'required',
			'email'					=>	'required|email'
		]);

		if(!$validation->fails()){
			$login					=	User::whereUsername($username)->get();
			
			if($login->count()){
				$login 				=	$login->first();
				$client 			= 	Client::where('login_id', '=', $login->id)
										->where('active', '=', 1)->get();	
			}

			if($client->count()){
				$client 			= $client->first();
				$client->fname 		= Input::get('fname');
				$client->lname 		= Input::get('lname');
				$client->contact_no = Input::get('contact_no');
				$client->address 	=	Input::get('address');
				$client->save();

				if($email = Input::get('email')){
					$login->email 	=	$email;
					$login->save();
				}
				
				$client 			= Client::where('login_id', '=', $login->id)
											->where('active', '=', 1)->get();
			
				return Response::json([
					'message'		=>	'Profile info have been updated.',
					'data'			=>	$this->clientsTransformer->transform($client->first())
				]);
			}
		}
		return Response::json([
			'errors' => [
				'message'			=>	'Profile info cannot be updated.',
				'errors_details'	=>	$validation->messages()
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
		$login						=	User::whereUsername($username)->get();
		if($login->count()){
			$client 				= Client::where('login_id', '=', $login->first()->id);

			if($client->count()){
				$client 			= $client->first();
				$client->active		=	0;
				$client->deleted	=	0;
				$client->save();

				return Response::json([
					'message' 		=> 'Account has been successfully deactivated.',
					'activate'		=>	(bool)$client->deleted
				]);
			}
		}
		return Response::json([
			'error' => [
				'message'			=>	'Client not registered or deactivated.'
			]
		]);
	}
}