<?php

class ImagesController extends \BaseController {

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
	public function store($username)
	{
		if(Input::hasFile('images')) {
		    $hairStyleImages = Input::file();
		    
		    // Make sure it really is an array
		    if (!is_array($hairStyleImages)) {
		        $hairStyleImages = array($hairStyleImages);
		    }

		    $totalNoOfImages = count($hairStyleImages);
		 	echo '<pre>'.print_r(Input::file('images'), true),'</pre>';
		 	return;
		    for ($i=0; $i < $totalNoOfImages ; $i++) { 
		    	$inputs['images'.$i] = $hairStyleImages[$i];
		    	$rules['images'.$i] = 'image|max:2048|mimes:jpeg,png';
		    }
		    $validator = Validator::make($inputs, $rules);

		    if(!$validator->fails()){

		    	$user = User::findByUsernameOrFail($username);
		    	var_dump($inputs);
		    	foreach ($inputs as $input) {
		    		$response = $hairStyleImage = HairStyleImage::create([
			    		'user_id' => $user->id,
			    		'image' => $input
			    	]);	

			    	if(!$response) return 'Something is wrong';
		    	}
		    	return;
		    }
	    	return Response::json([
	    		'errors' => $validator->messages()
	    		]
	    	);
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