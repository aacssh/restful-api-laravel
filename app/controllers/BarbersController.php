<?php
use \HairConnect\Transformers\BarbersTransformer;
use \HairConnect\Transformers\ImagesTransformer;
use \HairConnect\Services\ProfileService;
use \HairConnect\Validators\ValidationException;

class BarbersController extends TokensController {
	/**
	 * Stores object of BarbersTransformer
	 * @var BarbersTransformer
	 */
	protected $barbersTransformer;

	/**
	 * Stores object of ImagesTransformer
	 * @var ImagesTransformer
	 */
	protected $imagesTransformer;

	/**
	 * Stores object of APIController
	 * @var APIController
	 */
	protected $apiController;

	/**
	 * Stores object of ProfileService
	 * @var ProfileService
	 */
	protected $profileService;

	/**
	 * Prepare the object of the controller for use
	 * @param BarbersTransformer $barbersTransformer
	 * @param ImagesTransformer  $imagesTransformer 
	 * @param APIController      $apiController     
	 * @param ProfileService     $profileService    
	 */
	function __construct(BarbersTransformer $barbersTransformer, ImagesTransformer $imagesTransformer, APIController $apiController, ProfileService $profileService){
		$this->barbersTransformer 	=	$barbersTransformer;
		$this->imagesTransformer 	=	$imagesTransformer;
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
		$limit	 = Input::get('limit') ?: 5;
		$barbers = User::ofType('barber')->paginate($limit);
		$total 	 = $barbers->getTotal();

        return $this->apiController->respond([
            'data' 		=> 	$this->barbersTransformer->transformCollection($barbers->all()),
            'paginator'	=>	[
            	'total_count'	=>	$total,	
            	'total_pages'	=>	ceil($total/$barbers->getPerPage()),
            	'current_page'	=>	$barbers->getCurrentPage(),
            	'limit'			=>	(int)$limit,
            	'prev'			=>	$barbers->getLastPage()
            ]
        ]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $username
	 * @return Response
	 */
	public function show($username)
	{
		if( ($barber = $this->checkTokenAndUsernameExists(Input::get('token'), $username)) != false){
			$hsi	= 	HairStyleImages::where('user_id', '=', $barber->id)->get();

			$barberDetailsWithHairStyleImages = array_merge($this->barbersTransformer->transform($barber), [
				'hair_style_images'	=>	$this->imagesTransformer->transformCollection($hsi->all())
			]);

			return $this->apiController->respondSuccessWithDetails("{$username} data successfully retriveve", $barberDetailsWithHairStyleImages);
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
				$barberDetails = $this->profileService->update($username, Input::all());
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getErrors());
			}
			return $this->apiController->respondSuccessWithDetails('Profile info have been updated.', $this->barbersTransformer->transform($barberDetails));
		}
		return $this->apiController->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
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
			$barber->online			=	0;
			$barber->deactivated	=	0;
			$barber->save();

			return $this->apiController->respondSuccess('Account has been successfully deactivated.');
		}
		return $this->apiController->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	public function search()
	{
		$limit	 = Input::get('limit') ?: 5;

		if(($name = Input::get('name')) && !Input::get('city') && !Input::get('zip')){
			$name = explode(' ', $name);

			if(count($name) == 1){
				$name[1] = $name[0];
			}
			$barbers = User::where('fname', 'LIKE', '%'.$name[0].'%')
							->orWhere('lname', 'LIKE', '%'.$name[1].'%')->ofType('barber')->paginate($limit);
		}
		else if(!Input::get('name') && ($city = Input::get('city')) && !Input::get('zip'))
		{
			$barbers = User::where('address', 'LIKE', '%'.$city.'%')->ofType('barber')->paginate($limit);
		}
		else if(!Input::get('name') && !Input::get('city') && ($zip = Input::get('zip')))
		{
			$barbers = User::where('zip', '=', $zip)->ofType('barber')->paginate($limit);
		}
		else if(!Input::get('name') && ($city = Input::get('city')) && ($zip = Input::get('zip')))
		{
			$barbers = User::where('address', 'LIKE', '%'.$city.'%')->orWhere('zip', '=', $zip)->ofType('barber')->paginate($limit);
		}
		else if(($name = Input::get('name')) && ($city = Input::get('city')) && !Input::get('zip'))
		{
			$name = explode(' ', $name);

			if(count($name) == 1){
				$name[1] = $name[0];
			}
			$barbers = User::where('fname', 'LIKE', '%'.$name[0].'%')->orWhere('lname', 'LIKE', '%'.$name[1].'%')
							->orWhere('address', 'LIKE', '%'.$city.'%')->ofType('barber')->paginate($limit);	
		}
		else if(($name = Input::get('name')) && !Input::get('city') && ($zip = Input::get('zip')))
		{
			$name = explode(' ', $name);

			if(count($name) == 1){
				$name[1] = $name[0];
			}
			$barbers = User::where('fname', 'LIKE', '%'.$name[0].'%')->orWhere('lname', 'LIKE', '%'.$name[1].'%')
							->orWhere('zip', '=', $zip)->ofType('barber')->paginate($limit);	
		}else if(($name = Input::get('name')) && ($city =Input::get('city')) && ($zip = Input::get('zip'))){

			$name = explode(' ', $name);

			if(count($name) == 1){
				$name[1] = $name[0];
			}

			$barbers = User::where('fname', 'LIKE', '%'.$name[0].'%')
							->orWhere('lname', 'LIKE', '%'.$name[1].'%')
							->where('zip', '=', $zip)
							->where('address', 'LIKE', '%'.$city.'%')->ofType('barber')
							->paginate($limit);	
		}

		$total 	 = $barbers->getTotal();

        return $this->apiController->respond([
            'data' 		=> 	$this->barbersTransformer->transformCollection($barbers->all()),
            'paginator'	=>	[
            	'total_count'	=>	$total,	
            	'total_pages'	=>	ceil($total/$barbers->getPerPage()),
            	'current_page'	=>	$barbers->getCurrentPage(),
            	'limit'			=>	(int)$limit,
            	'prev'			=>	$barbers->getLastPage()
            ]
        ]);
	}
}