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
		if(is_object($token = User::findByTokenAndUsernameOrFail(Input::get('token'), Input::get('username')))){
			$token->access_token = NULL;

			if($token->save()){
				return $this->apiController->respond([
					'message' => 'Successfully logged out.'
				]);
			}
		}
		return $this->apiController->respond([
			'message' => 'Could not logged out. Try again'
		]);
	}

	public function update()
	{
		if(is_object($token = User::findByTokenAndUsernameOrFail(Input::get('token'), Input::get('username')))){
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
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token'
            ]
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
}