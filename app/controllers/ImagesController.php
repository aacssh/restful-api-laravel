<?php

class ImagesController extends \BaseController {

	protected $user;

	function __construct(User $user){
		$this->user = $user;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($username){
		if(Input::hasFile('images')) {
	    $hairStyleImages = Input::file('images');

	    // Make sure it really is an array
	    if (!is_array($hairStyleImages)) {
	      $hairStyleImages = array($hairStyleImages);
	    }

		  $totalNoOfImages = count($hairStyleImages);
		  $inputs = [];
		  $rules = [];

	    for ($i = 0; $i < $totalNoOfImages ; $i++) { 
	    	$inputs['images'.$i] = $hairStyleImages[$i];
				$rules['images'.$i] = 'image|max:4096|mimes:jpeg,png';
	    }
	    $validator = Validator::make($inputs, $rules);

	    if(!$validator->fails()){
				$user = $this->user->findByUsernameOrFail($username);

	    	foreach ($inputs as $input) {
	    		$fileName = $input->getClientOriginalName();
	    		$mainFile = '/Images/'.time().'-'.$fileName;
	    		$thumbnail = '/Images/'.'thumbnail-'.time().'-'.$fileName;

	    		$response = HairStyleImage::create([
		    		'user_id' => $user->id,
		    		'image' => $mainFile
		    	]);

					$image = Image::make($input->getRealPath());
					$image->resize(800, null, function ($constraint) {
										$constraint->aspectRatio();
									})
								->crop(650, 650)
								->save(base_path().'/app/HairConnect'.$mainFile)
								->resize(250, null, function ($constraint) {
								    $constraint->aspectRatio();
									})
								->save(base_path().'/app/HairConnect'.$thumbnail);

		    	if(!$response) return 'Something is wrong';
	    	}
	    	return 'Saved';
	    }
    	return Response::json([
    		'errors' => $validator->messages()
    	]);
		}
		return Response::json([
	    		'errors' => 'Data should be images.'
	    		]
	    );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}
}