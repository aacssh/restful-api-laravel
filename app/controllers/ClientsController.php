<?php
use \HairConnect\Transformers\ClientsTransformer;
use \HairConnect\Services\ProfileService;
use \HairConnect\Exceptions\ValidationException;

class ClientsController extends TokensController {

	/**
	 * @var ClientsTransformer
	 */
	protected $transformer;

	/**
	 * @var APIResponse
	 */
	protected $api;

	/**
	 * @var ProfileService
	 */
	protected $service;	

	/**
	 * [__construct description]
	 * @param ClientsTransformer $transformer [description]
	 */
	function __construct(ClientsTransformer $transformer, APIResponse $api, ProfileService $service){
		$this->transformer = $transformer;
		$this->api = $api;
		$this->service = $service;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$limit = Input::get('limit') ?: 5;
    $clients = User::ofType('client')->paginate($limit);
    $total = $clients->getTotal();

    return $this->api->respond([
    	'success' => [
    		'message' => 'Successfulll retriveved',
    		'status_code' => 200,
        'data' 	=>	$this->transformer->transformCollection($clients->all()),
        'paginator'	=>	[
        	'total_count'	=>	$total,	
        	'total_pages'	=>	ceil($total/$clients->getPerPage()),
        	'current_page'	=>	$clients->getCurrentPage(),
        	'limit'  =>	(int)$limit,
        	'prev'  =>	$clients->getLastPage()
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
			return $this->api->respondSuccessWithDetails("{$username} details successfully retrieve", $this->transformer->transform($client));
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
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
				$client = $this->service->update($username, Input::all());
			}catch(ValidationException $e){
				return $this->api->respondInvalidParameters($e->getErrors());
			}
			return $this->api->respondSuccessWithDetails('Profile successfully updated.', $this->transformer->transform($client));
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
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
			$client->online	= 0;
			$client->deactivated = 0;
			$client->save();
			return $this->api->respondSuccess('Account has been successfully deactivated.');
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}
}