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
				'message'	=>	'There is no client associated with given username.'
			]
		]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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