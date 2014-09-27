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
	public function index(){
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
	public function show($username){
		try{
			$this->service->show(Input::all(), $username);
			return $this->api->respondSuccessWithDetails("{$username} details successfully retrieve", $this->transformer->transform($this->service->getProfileDetails()));
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $username
	 * @return Response
	 */
	public function update($username){
		try{
			$client = $this->service->update(Input::all(), $username);
			return $this->api->respondSuccessWithDetails('Profile successfully updated.', $this->transformer->transform($this->service->getProfileDetails()));
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string 	$username
	 * @return Response
	 */
	public function destroy($username){
		try{
			$this->service->destroy(Input::all(), $username);
			return $this->api->respondSuccess('Account has been successfully deactivated.');
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());	
		}
	}
}