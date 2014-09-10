<?php
use \HairConnect\Transformers\BarbersTransformer;
use \HairConnect\Transformers\ImagesTransformer;

class BarbersController extends \BaseController {

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
	 * [__construct description]
	 * @param BarbersTransformer $barbersTransformer [description]
	 */
	function __construct(BarbersTransformer $barbersTransformer, ImagesTransformer $imagesTransformer, APIController $apiController){
		$this->barbersTransformer 	=	$barbersTransformer;
		$this->imagesTransformer 	=	$imagesTransformer;
		$this->apiController 		=	$apiController;
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
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

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $username
	 * @return Response
	 */
	public function show($username)
	{
		$barber	   =	User::findByUsernameOrFail($username)->barber;
		if($barber->count()){
			$hsi	= 	HairStyleImages::where('barber_id', '=', $barber->id)->get();

			return $this->apiController->respond([
				'details' 			=> 	$this->barbersTransformer->transform($barber),
				'hair_style_images'	=>	$this->imagesTransformer->transformCollection($hsi->all())
			]);
		}
		//$barber = User::whereUsername($username)->where('group', '=', 0)->where('active', '=', 1);

		return $this->apiController->respondNotFound('Barber cannot be found or the account is deactivated.');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $username
	 * @return Response
	 */
	public function update($username)
	{
		$validation = Validator::make(Input::all(), [
			'fname'			=> 	'required|Alpha',
			'lname'			=>	'required|Alpha',
			'contact_no'	=>	'required|numeric',
			'address'		=>	'required',
			'email'			=>	'required|email'
		]);

		if(!$validation->fails()){
			$user	=	User::findByUsernameOrFail($username);
			$barber =	$user->barber;

			if($barber->count()){
				$barber->fname 		= 	Input::get('fname');
				$barber->lname 		= 	Input::get('lname');
				$barber->contact_no = 	Input::get('contact_no');
				$barber->address 	=	Input::get('address');
				$barber->save();

				$user->email 		=	Input::get('email');
				$user->save();
			
				return $this->apiController->respond([
					'message'	=>	'Profile info have been updated.',
					'data'		=>	$this->barbersTransformer->transform($barber)
				]);
			}
		}
		return $this->apiController->respondInvalidParameters($validation->messages());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function destroy($username)
	{
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
}