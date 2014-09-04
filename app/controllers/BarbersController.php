<?php
use \HairConnect\Transformers\BarbersTransformer;
use \HairConnect\Transformers\ImagesTransformer;

class BarbersController extends UsersController {

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
        $barbers = User::where('group', '=', 0)->get();
        return Response::json([
            'data' => $this->barbersTransformer->transformCollection($barbers->all()),
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
		$barber = User::whereUsername($username)->where('group', '=', 0)->where('active', '=', 1);
		
		if($barber->count()){			
			$hsi = HairStyleImages::where('barber_id', '=', $barber->first()->id)->get();

			return Response::json([
				'details' 			=> 	$this->barbersTransformer->transform($barber->first()),
				'hair_style_images'	=>	$this->imagesTransformer->transformCollection($hsi->all())
			]);
		}

		return Response::json([
			'errors' => [
				'message'	=>	'Barber cannot be found or the account is deactivated.'
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
			'fname'			=> 	'required|Alpha',
			'lname'			=>	'required|Alpha',
			'contact_no'	=>	'required|numeric',
			'address'		=>	'required',
			'email'			=>	'required|email'
		]);

		if(!$validation->fails()){
			$barber = User::whereUsername($username)->where('group', '=', 0)->where('active', '=', 1)->get();

			if($barber->count()){
				$barber = $barber->first();
				$barber->fname = Input::get('fname');
				$barber->lname = Input::get('lname');
				$barber->contact_no = Input::get('contact_no');
				$barber->address 	=	Input::get('address');
				$barber->email 		=	Input::get('email');
				$barber->save();

				$barber = User::whereUsername($username)->where('active', '=', 1)->where('group', '=', 0)->get();
			
				return Response::json([
					'message'	=>	'Profile info have been updated.',
					'data'		=>	$this->barbersTransformer->transform($barber->first())
				]);
			}
		}
		return Response::json([
			'errors' => [
				'message'			=>	'Profile info cannot be updated.',
				'errors_details'	=>	$validation->messages()
			]
		]);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}