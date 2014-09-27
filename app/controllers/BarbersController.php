<?php
use \HairConnect\Transformers\BarbersTransformer;
use \HairConnect\Transformers\ImagesTransformer;
use \HairConnect\Services\ProfileService;
use \HairConnect\Exceptions\ValidationException;

class BarbersController extends TokensController {
	/**
	 * Stores object of BarbersTransformer
	 * @var BarbersTransformer
	 */
	protected $transformer;

	/**
	 * Stores object of APIController
	 * @var APIController
	 */
	protected $api;

	/**
	 * Stores object of ProfileService
	 * @var ProfileService
	 */
	protected $service;

	protected $imageTransformer;

	/**
	 * Prepare the object of the controller for use
	 * @param BarbersTransformer $transformer
	 * @param ImagesTransformer  $imagesTransformer 
	 * @param APIController      $apiController     
	 * @param ProfileService     $profileService    
	 */
	function __construct(BarbersTransformer $transformer, APIResponse $api, ProfileService $service, ImagesTransformer $imagesTransformer){
		$this->transformer = $transformer;
		$this->api = $api;
		$this->service = $service;
		$this->imageTransformer = $imagesTransformer;
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$limit = Input::get('limit') ?: 5;
		$barbers = User::ofType('barber')->paginate($limit);
		$total = $barbers->getTotal();

    return $this->api->respond([
    	'success' => [
    		'message' => 'Successfulll retriveved',
    		'status_code' => 200,
        'data' =>  $this->transformer->transformCollection($barbers->all()),
        'paginator'	=>	[
        	'total_count'	=>	$total,	
        	'total_pages'	=>	ceil($total/$barbers->getPerPage()),
        	'current_page'	=>	$barbers->getCurrentPage(),
        	'limit'			=>	(int)$limit,
        	'prev'			=>	$barbers->getLastPage()
        ]
    	]
    ]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $username
	 * @return Response
	 */
	public function show($username){
		try{
			$this->service->show(Input::all(), $username);
			return $this->api->respondSuccessWithDetails("{$username} data successfully retriveve", $this->transformer->transformWithImages($this->service->getProfileDetails()));
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
			$this->service->update($username, Input::all());
			return $this->api->respondSuccessWithDetails('Profile successfully updated.', $this->transformer->transform($this->service->getProfileDetails()));
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());	
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $username
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

	public function search(){
		$limit = Input::get('limit') ?: 5;
		$name = explode(' ', Input::get('name'));
		$city = Input::get('city');
		$zip = Input::get('zip');

		if(count($name) == 1){
			$name[1] = $name[0];
		}

		$barbers = User::ofType('barber')->where(
					function($query) use ($name, $zip, $city)
					{
						$query->where('fname', 'LIKE', '%'.$name[0].'%')->orWhere('lname', 'LIKE', '%'.$name[1].'%')
							  ->orWhere('zip', '=', $zip)->orWhere('address', 'LIKE', '%'.$city.'%');
					})->paginate($limit);
		$total = $barbers->getTotal();

    return $this->api->respond([
    	'success' => [
    		'message' => 'Successfulll retriveved',
    		'status_code' => 200,
        'data'  => 	$this->transformer->transformCollection($barbers->all()),
        'paginator'	=>	[
        	'total_count'	=>	$total,	
        	'total_pages'	=>	ceil($total/$barbers->getPerPage()),
        	'current_page'	=>	$barbers->getCurrentPage(),
        	'limit'  =>	(int)$limit,
        	'prev'  =>	$barbers->getLastPage()
        ]
    	]
    ]);
	}
}