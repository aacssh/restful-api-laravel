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
	 * [__construct description]
	 * @param BarbersTransformer $barbersTransformer [description]
	 */
	function __construct(BarbersTransformer $barbersTransformer, ImagesTransformer $imagesTransformer){
		$this->barbersTransformer 	=	$barbersTransformer;
		$this->imagesTransformer 	=	$imagesTransformer;	 
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
        return Response::json([
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
		$login_id						=	User::whereUsername($username)->get();
		if($login_id->count()){
			$barber 					= 	Barber::where('login_id', '=', $login_id->first()->id);

			if($barber->count()){
				$hsi 					= 	HairStyleImages::where('barber_id', '=', $barber->first()->id)->get();
				
				return Response::json([
					'details' 			=> 	$this->barbersTransformer->transform($barber->first()),
					'hair_style_images'	=>	$this->imagesTransformer->transformCollection($hsi->all())
				]);
			}
		}		
		//$barber = User::whereUsername($username)->where('group', '=', 0)->where('active', '=', 1);

		return Response::json([
			'errors' => [
				'message'				=>	'Barber cannot be found or the account is deactivated.'
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
		$validation = Validator::make(Input::all(), [
			'fname'						=> 	'required|Alpha',
			'lname'						=>	'required|Alpha',
			'contact_no'				=>	'required|numeric',
			'address'					=>	'required',
			'email'						=>	'required|email'
		]);

		if(!$validation->fails()){
			$login						=	User::whereUsername($username)->get();
			
			if($login->count()){
				$login 					=	$login->first();
				$barber 				= 	Barber::where('login_id', '=', $login->id)
													->where('active', '=', 1)->get();	
			}

			if($barber->count()){
				$barber 				= $barber->first();
				$barber->fname 			= Input::get('fname');
				$barber->lname 			= Input::get('lname');
				$barber->contact_no 	= Input::get('contact_no');
				$barber->address 		=	Input::get('address');
				$barber->save();

				if($email = Input::get('email')){
					$login->email 		=	$email;
					$login->save();
				}
				
				$barber 				= Barber::where('login_id', '=', $login->id)
												->where('active', '=', 1)->get();
			
				return Response::json([
					'message'			=>	'Profile info have been updated.',
					'data'				=>	$this->barbersTransformer->transform($barber->first())
				]);
			}
		}
		return Response::json([
			'errors' => [
				'message'				=>	'Profile info cannot be updated.',
				'errors_details'		=>	$validation->messages()
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
		$login							=	User::whereUsername($username)->get();
		if($login->count()){
			$barber 					= 	Barber::where('login_id', '=', $login->first()->id);

			if($barber->count()){
				$barber 				= 	$barber->first();
				$barber->active			=	0;
				$barber->deleted		=	0;
				$barber->save();

				return Response::json([
					'message' 			=> 'Account has been successfully deactivated.',
					'activate'			=>	(bool)$barber->deleted
				]);
			}
		}
		return Response::json([
			'error' => [
				'message'				=>	'Barber not registered or deactivated.'
			]
		]);
	}
}