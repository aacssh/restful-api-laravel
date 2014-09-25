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

	/**
	 * Prepare the object of the controller for use
	 * @param BarbersTransformer $transformer
	 * @param ImagesTransformer  $imagesTransformer 
	 * @param APIController      $apiController     
	 * @param ProfileService     $profileService    
	 */
	function __construct(BarbersTransformer $transformer, APIResponse $api, ProfileService $service){
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
	public function show($username, ImagesTransformer $imagesTransformer)
	{
		if( ($barber = $this->checkTokenAndUsernameExists(Input::get('token'), $username)) != false){
			$hsi = HairStyleImage::where('user_id', '=', $barber->id)->get();

			$barberDetailsWithHairStyleImages = array_merge($this->transformer->transform($barber), [
				'hair_style_images'	=>	$imagesTransformer->transformCollection($hsi->all())
			]);
			return $this->api->respondSuccessWithDetails("{$username} data successfully retriveve", $barberDetailsWithHairStyleImages);
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
				$barberDetails = $this->service->update($username, Input::all());
			}catch(ValidationException $e){
				return $this->api->respondInvalidParameters($e->getErrors());
			}
			return $this->api->respondSuccessWithDetails('Profile info have been updated.', $this->transformer->transform($barberDetails));
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function destroy($username)
	{
		if(( $barber = $this->checkTokenAndUsernameExists(Input::get('token'), $username) != false)){
			$barber->online	= 0;
			$barber->deactivated = 0;
			$barber->save();
			return $this->api->respondSuccess('Account has been successfully deactivated.');
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	public function search()
	{
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