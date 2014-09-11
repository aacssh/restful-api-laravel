<?php
use \HairConnect\Transformers\UsersTransformer;

class UsersController extends \BaseController {
    /**
     * @var
     */
    protected $usersTransformer;


    /**
     * @param UsersTransformer $usersTransformer
     */
    public function __construct(UsersTransformer $usersTransformer){
        $this->usersTransformer = $usersTransformer;
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $validator = Validator::make(Input::all(),[
            'name'              =>  'required',
            'username' 			=>  'required|max:20|min:2|unique:users',
            'password' 			=>  'required|min:6',
            'confirm_password' 	=>  'required|same:password',
            'email' 			=>  'required|max:60|email|unique:users'
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

	public function loginWithFacebook()
	{
		// get data from input
	    $code = Input::get( 'code' );

	    // get fb service
	    $fb = OAuth::consumer( 'Facebook' );

	    // check if code is valid

	    // if code is provided get user data and sign in
	    if ( !empty( $code ) ) {

	        // This was a callback request from facebook, get the token
	        $token = $fb->requestAccessToken( $code );

	        // Send a request with it
	        $result = json_decode( $fb->request( '/me' ), true );

	        $message = 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
	        echo $message. "<br/>";

	        //Var_dump
	        //display whole array().
	        dd($result);

	    }
	    // if not ask for permission first
	    else {
	        // get fb authorization
	        $url = $fb->getAuthorizationUri();

	        // return to facebook login url
	         return Redirect::to( (string)$url );
	    }
	}
}