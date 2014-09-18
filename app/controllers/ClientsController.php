<?php
use \HairConnect\Transformers\ClientsTransformer;
use \HairConnect\Services\ProfileService;
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
	 * [$profileService description]
	 * @var [type]
	 */
	protected $profileService;	

	/**
	 * [__construct description]
	 * @param ClientsTransformer $clientsTransformer [description]
	 */
	function __construct(ClientsTransformer $clientsTransformer, APIController $apiController, ProfileService $profileService){
		$this->clientsTransformer 	= 	$clientsTransformer;
		$this->apiController 		=	$apiController;
		$this->profileService 		=	$profileService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($barber = null)
	{
		$limit		=	Input::get('limit') ?: 5;
        $clients 	= 	User::ofType('client')->paginate($limit);
        $total 		=	$clients->getTotal();

        return $this->apiController->respond([
            'data' 	=>	$this->clientsTransformer->transformCollection($clients->all()),
            'paginator'	=>	[
            	'total_count'	=>	$total,	
            	'total_pages'	=>	ceil($total/$clients->getPerPage()),
            	'current_page'	=>	$clients->getCurrentPage(),
            	'limit'			=>	(int)$limit,
            	'prev'			=>	$clients->getLastPage()
            ]
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
		if(($client = $this->checkToken(Input::get('token'), $username) != false)){
			return $this->apiController->respond([
				'details'	=> 	$this->clientsTransformer->transform($client)
			]);
		}
		return $this->apiController->respond([
			'errors' => [
            	'message' => 'Invalid token or User cannot be found.'
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
		if($this->checkToken(Input::get('token'), $username) != false){
			try{
				$client = $this->profileService->update($username, Input::all());

				return $this->apiController->respond([
					'message'	=>	'Profile info have been updated.',
					'data'		=>	$this->clientsTransformer->transform($client)
				]);
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getErrors());
			}
		}
		return $this->apiController->respond([
			'errors' => [
            	'message' => 'Invalid token or User cannot be found.'
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
		if(($client = $this->checkToken(Input::get('token'), $username)) != false){
			$client->online			=	0;
			$client->deactivated	=	0;
			$client->save();

			return $this->apiController->respond([
				'message' 	=> 'Account has been successfully deactivated.',
				'activate'	=>	(bool)$client->deactivated
			]);
		}
		return $this->apiController->respond([
			'errors' => [
            	'message' => 'Invalid token or User cannot be found.'
            ]
        ]);
	}
}