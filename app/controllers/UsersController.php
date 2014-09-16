<?php
use \HairConnect\Transformers\UsersTransformer;
use \HairConnect\Services\UserService;
use \HairConnect\Validators\ValidationException;

class UsersController extends \BaseController {
    /**
     * @var
     */
    protected $usersTransformer;

    /**
     * [$userService description]
     * @var [type]
     */
    protected $userService;

    /**
     * [$apiController description]
     * @var [type]
     */
    protected $apiController;

    /**
     * @param UsersTransformer $usersTransformer
     */
    public function __construct(UsersTransformer $usersTransformer, UserService $userService, APIController $apiController){
        $this->usersTransformer = $usersTransformer;
        $this->userService 		= $userService;
        $this->apiController 	= $apiController;
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function register()
	{
		try{
        	$this->userService->make(Input::all());
        }catch(ValidationException $e){
        	return $this->apiController->respondInvalidParameters($e->getErrors());
        }

        return $this->apiController->respond([
			'message'		=>	'User has been successfully registered.'
		]);
	}

	public function login()
	{
		try{
        	if(!$this->userService->login(Input::all())){
        		return $this->apiController->respond([
        			'error' => [
						'message' => 'Password or email does not match.'
					]
				]);
        	}
        }catch(ValidationException $e){
        	return $this->apiController->respondInvalidParameters($e->getErrors());
        }
        return $this->apiController->respond([
			'message' 		=> 'Successfully logged in.',
			'access_token' 	=> $this->userService->getToken()
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy()
	{
		$saveToken = User::findByUsernameOrFail(Input::get('username'));
		$saveToken->access_token = NULL;

		if($saveToken->save()){
			return $this->apiController->respond([
				'message' => 'Successfully logged out.'
			]);
		}
		return $this->apiController->respond([
			'message' => 'Could not logged out. Try again'
		]);
	}

	public function update()
	{
		try{
			if(!$this->userService->update(Input::all())){
				return $this->apiController->respondNotFound('Invalid old password.');
			}
		}catch(ValidationException $e){
			return $this->apiController->respondInvalidParameters($e->getErrors());
		}
		return $this->apiController->respond([
			'message' => 'Password successfully changed.'
		]);
	}

	public function forgotPassword()
	{
		try{
			if(!$this->userService->forgotPassword(Input::all())){
				return $this->apiController->respond([
					'message' => 'Email does not exist.'
				]);
			}
		}catch(ValidationException $e){
			return $this->apiController->respondInvalidParameters($e->getErrors());
		}
		return $this->apiController->respond([
			'message' => 'Check your email for new password.'
		]);
	}

	public function recover($code)
	{
		try{
			$this->userService->recover(Input::all(), $code);
		}catch(ValidationException $e){
			return $this->apiController->respondInvalidParameters($e->getErrors());
		}

		return $this->apiController->respond([
			'message' => 'Your account has been recovered. Sign in with your new password'
		]);
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