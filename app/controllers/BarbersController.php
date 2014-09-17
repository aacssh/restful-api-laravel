<?php
use \HairConnect\Transformers\BarbersTransformer;
use \HairConnect\Transformers\ImagesTransformer;
use \HairConnect\Services\BarberService;
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
	protected $barberService;

	/**
	 * [__construct description]
	 * @param BarbersTransformer $barbersTransformer [description]
	 */
	function __construct(BarbersTransformer $barbersTransformer, ImagesTransformer $imagesTransformer, APIController $apiController, BarberService $barberService){
		$this->barbersTransformer 	=	$barbersTransformer;
		$this->imagesTransformer 	=	$imagesTransformer;
		$this->apiController 		=	$apiController;
		$this->barberService 		=	$barberService;
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if($this->checkToken(Input::get('token'))){
			$limit		=	Input::get('limit') ?: 5;
	        $barbers 	= 	Barber::paginate($limit);
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
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token'
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
		if($this->checkToken(Input::get('token'), $username)){
			$barber	   =	User::findByUsernameOrFail($username)->barber;
			if($barber->count()){
				$hsi	= 	HairStyleImages::where('barber_id', '=', $barber->id)->get();

				return $this->apiController->respond([
					'details' 			=> 	$this->barbersTransformer->transform($barber),
					'hair_style_images'	=>	$this->imagesTransformer->transformCollection($hsi->all())
				]);
			}
			return $this->apiController->respondNotFound('Barber cannot be found or the account is deactivated.');
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token'
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
		if($this->checkToken(Input::get('token'), $username)){
			try{
				$barberDetails = $this->barberService->update($username, Input::all());

				return $this->apiController->respond([
					'message'	=>	'Profile info have been updated.',
					'data'		=>	$this->barbersTransformer->transform($barberDetails)
				]);
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getErrors());
			}
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token'
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
		if($this->checkToken(Input::get('token'), $username)){
			$barber    =    User::findByUsernameOrFail($username)->barber;
			if($barber->count()){
				$barber->active		=	0;
				$barber->deleted	=	0;
				$barber->save();

				return $this->apiController->respond([
					'message' 	=> 'Account has been successfully deactivated.',
					'activate'	=>	(bool)$barber->deleted
				]);
			}
			return $this->apiController->respondNotFound('Barber not registered or deactivated.');
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token'
            ]
        ]);
	}

	public function search()
	{
		if(Input::get('name')){
			$name = explode(' ', Input::get('name'));

			if(count($name) == 1){
				$name[1] = Input::get('name');
			}
		}else{
			$name = ['', ''];
		}

		$barber = Barber::orWhere('fname', 'LIKE', '%'.$name[0].'%')->orWhere('lname', 'LIKE', '%'.$name[1].'%')
					->orWhere('zip', '=', Input::get('zip'))
					->orWhere('address', 'LIKE', '%'.Input::get('city').'%')->get();

		return $this->apiController->respond([
            'data' 		=> 	$this->barbersTransformer->transformCollection($barber->all())
        ]);
	}
}