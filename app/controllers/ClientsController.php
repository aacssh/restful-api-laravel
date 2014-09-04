<?php
use \HairConnect\Transformers\ClientsTransformer;

class ClientsController extends UsersController {

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
		$this->clientsTransformer = $clientsTransformer;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = User::where('group', '=', 1)->get();
        return Response::json([
            'data' => $this->clientsTransformer->transformCollection($users->all())
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
		$client = User::whereUsername($username)->where('active', '=', 1);
		
		if($client->count()){
			return Response::json([
				'details' 	=> 	$this->clientsTransformer->transform($client->first())
			]);
		}

		return Response::json([
			'errors' => [
				'message'	=>	'Client cannot be found or the account is deactivated.'
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
			'fname'			=> 	'required|Alpha',
			'lname'			=>	'required|Alpha',
			'contact_no'	=>	'required|numeric',
			'address'		=>	'required',
			'email'			=>	'required|email'
		]);

		if(!$validation->fails()){
			$client = User::whereUsername($username)->where('active', '=', 1)->where('group', '=', 1)->get();

			if($client->count()){
				$client = $client->first();
				$client->fname = Input::get('fname');
				$client->lname = Input::get('lname');
				$client->contact_no = Input::get('contact_no');
				$client->address 	=	Input::get('address');
				$client->email 		=	Input::get('email');
				$client->save();

				$client = User::whereUsername($username)->where('active', '=', 1)->where('group', '=', 1)->get();
			
				return Response::json([
					'message'	=>	'Profile info have been updated.',
					'data'		=>	$this->clientsTransformer->transform($client->first())
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
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}