<?php
use \HairConnect\Transformers\BarbersTransformer;
use \HairConnect\Transformers\ImagesTransformer;

class BarbersController extends UsersController {

	/**
	 * [$barbersTransformer description]
	 * @var [type]
	 */
	protected $barbersTransformer;

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
		//return Request::url();
        $barbers = User::where('group', '=', 0)->get();
        return Response::json([
            'data' => $this->barbersTransformer->transformCollection($barbers->all())
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
		$barber = User::whereUsername($username)->where('active', '=', 1);
		
		if($barber->count()){
			//echo '<pre>', print_r($barber->first(), true),'</pre>';
			
			$hsi = HairStyleImages::where('barber_id', '=', $barber->first()->id)->get();

			return Response::json([
				'details' 			=> 	$this->barbersTransformer->transform($barber->first()),
				'hair_style_images'	=>	$this->imagesTransformer->transformCollection($hsi->all())
			]);
		}

		return Response::json([
			'errors' => [
				'message'	=>	'There is no barber associated with given username.'
			]
		]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		
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
