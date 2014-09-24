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
	public function index()
	{
		$limit		=	Input::get('limit') ?: 5;
        $clients 	= 	User::ofType('client')->paginate($limit);
        $total 		=	$clients->getTotal();

        return $this->apiController->respond([
        	'success' => [
        		'message' => 'Successfulll retriveved',
        		'status_code' => 200,
	            'data' 	=>	$this->clientsTransformer->transformCollection($clients->all()),
	            'paginator'	=>	[
	            	'total_count'	=>	$total,	
	            	'total_pages'	=>	ceil($total/$clients->getPerPage()),
	            	'current_page'	=>	$clients->getCurrentPage(),
	            	'limit'			=>	(int)$limit,
	            	'prev'			=>	$clients->getLastPage()
	            ]
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
		if(($client = $this->checkTokenAndUsernameExists(Input::get('token'), $username) != false)){
			return $this->apiController->respondSuccessWithDetails("{$username} details successfully retrieve", $this->clientsTransformer->transform($client));
		}
		return $this->apiController->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $username
	 * @return Response
	 */
	public function update($username)
	{
		if($this->checkTokenAndUsernameExists(Input::get('token'), $username) != false){
			try{
				$client = $this->profileService->update($username, Input::all());
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getErrors());
			}
			return $this->apiController->respondSuccessWithDetails('Profile successfully updated.', $this->clientsTransformer->transform($client));
		}
		return $this->apiController->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string 	$username
	 * @return Response
	 */
	public function destroy($username)
	{
		if(($client = $this->checkTokenAndUsernameExists(Input::get('token'), $username)) != false){
			$client->online			=	0;
			$client->deactivated	=	0;
			$client->save();

			return $this->apiController->respondSuccess('Account has been successfully deactivated.');
		}
		return $this->apiController->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}
}