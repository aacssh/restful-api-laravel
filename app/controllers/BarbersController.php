<?php
use \HairConnect\Transformers\BarbersTransformer;
use \HairConnect\Transformers\ImagesTransformer;
use \HairConnect\Services\ProfileService;
use \HairConnect\Validators\ValidationException;

class BarbersController extends TokensController {
	/**
	 * [$barbersTransformer description]
	 * @var [type]
	 */
	protected $barbersTransformer;

	/**
	 * [$imagesTransformer description]
	 * @var [type]
	 */
	protected $imagesTransformer;

	/**
	 * [$apiController description]
	 * @var [type]
	 */
	protected $apiController;

	/**
	 * [$barberService description]
	 * @var [type]
	 */
	protected $profileService;

	/**
	 * [__construct description]
	 * @param BarbersTransformer $barbersTransformer [description]
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
		$limit		=	Input::get('limit') ?: 5;
        $barbers 	= 	User::ofType('barber')->paginate($limit);
        $total 		=	$barbers->getTotal();

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
		if( ($barber = $this->checkToken(Input::get('token'), $username)) != false){			
			$hsi	= 	HairStyleImages::where('user_id', '=', $barber->id)->get();

			return $this->apiController->respond([
				'details' 			=> 	$this->barbersTransformer->transform($barber),
				'hair_style_images'	=>	$this->imagesTransformer->transformCollection($hsi->all())
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
				$barberDetails = $this->profileService->update($username, Input::all());

				return $this->apiController->respond([
					'message'	=>	'Profile info have been updated.',
					'data'		=>	$this->barbersTransformer->transform($barberDetails)
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
	 * @param  string  $username
	 * @return Response
	 */
	public function destroy($username)
	{
		if(( $barber = $this->checkToken(Input::get('token'), $username) != false)){
			$barber->online			=	0;
			$barber->deactivated	=	0;
			$barber->save();

			return $this->apiController->respond([
				'message' 	=> 'Account has been successfully deactivated.',
				'activate'	=>	(bool)$barber->deleted
			]);
		}
		return $this->apiController->respond([
			'errors' => [
            	'message' => 'Invalid token or User cannot be found.'
            ]
        ]);
	}

	public function search()
	{
		$name = explode(' ', Input::get('name'));

		if(count($name) == 1){
			$name[1] = Input::get('name');
		}
		$barber = User::where('fname', 'LIKE', '%'.$name[0].'%')
					->orWhere('lname', 'LIKE', '%'.$name[1].'%')
					->orWhere('zip', '=', Input::get('zip'))
					->orWhere('address', '=', Input::get('city'))->ofType('barber')
					->get();

		return $this->apiController->respond([
            'data' 		=> 	$this->barbersTransformer->transformCollection($barber->all())
        ]);
	}
}