<?php
use \HairConnect\Services\UserService;
use \HairConnect\Transformers\UsersTransformer;
use \HairConnect\Validators\ValidationException;

class UsersController extends TokensController {

    /**
     * This stores the object of Service class
     * @var object
     */
    protected $userService;

    /**
     * This stores the object of ApiController class
     * @var [type]
     */
    protected $apiController;

    /**
     * This constructor injects the dependencies of the class
     * @param UserService   $userService  
     * @param APIController $apiController
     */
    public function __construct(UserService $userService, APIController $apiController, UsersTransformer $usersTransformer){
        $this->userService 		= $userService;
        $this->apiController 	= $apiController;
        $this->usersTransformer = $usersTransformer;
    }

	/**
	 * This function stores a newly created resource(User in this application) in database.
	 *
	 * @return Response
	 */
	public function register()
	{
		try{
        	$this->userService->make(Input::all());
        }catch(ValidationException $e){
        	return $this->apiController->respondInvalidParameters($e->getMessage());
        }
        return $this->apiController->respondSuccessWithDetails(
        	'User has been successfully registered.', $this->usersTransformer->transform($this->userService->getUserDetails())
        );
	}

	/**
	 * This function logs in user into the system
	 * @return Response
	 */
	public function login()
	{
		try{
        	$this->userService->login(Input::all());
        }catch(ValidationException $e){
        	return $this->apiController->respondInvalidParameters($e->getMessage());
        }
        return $this->apiController->respondSuccessWithDetails(
        	'Successfully logged in.', $this->usersTransformer->transform($this->userService->getUserDetails())
        );
	}

	/**
	 * This function logs out the user from the system
	 *
	 * @return Response
	 */
	public function destroy()
	{
		if(($token = $this->checkTokenAndUsernameExists(Input::get('token'), Input::get('username'))) != false){
			$token->access_token = NULL;
			if($token->save()){
				return $this->apiController->respondSuccess('Successfully logged out.');
			}
		}
		return $this->apiController->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * This function updates the password of a user
	 * @return Response
	 */
	public function update()
	{
		if(($token = $this->checkTokenAndUsernameExists(Input::get('token'), Input::get('username'))) != false){
			try{
				$this->userService->update(Input::all());
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getMessage());
			}
			return $this->apiController->respondSuccess('Password successfully changed.');
		}
		return $this->apiController->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * This function generate a new password and sends it to user' email along with an activation link
	 * @return Response
	 */
	public function forgotPassword()
	{
		try{
			if(!$this->userService->forgotPassword(Input::all())){
				return $this->apiController->respondInvalidParameters(['Email does not exist.']);
			}
		}catch(ValidationException $e){
			return $this->apiController->respondInvalidParameters($e->getMessage());
		}
		return $this->apiController->respondSuccess('Check your email for new password.');
	}

	/**
	 * This function executes when use clicks the activation link sent to his/her email. 
	 * This function  resets old password with new password sent to email.
	 * @param  string $code
	 * @return Response
	 */
	public function recover($code)
	{
		try{
			$this->userService->recover(Input::all(), $code);
		}catch(ValidationException $e){
			return $this->apiController->respondInvalidParameters($e->getMessage());
		}
		return $this->apiController->respondSuccess('Your account has been recovered. Sign in with your new password');
	}
}