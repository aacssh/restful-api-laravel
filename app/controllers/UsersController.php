<?php
use \HairConnect\Services\UserService;
use \HairConnect\Transformers\UsersTransformer;
use \HairConnect\Validators\ValidationException;

class UsersController extends TokensController {

    /**
     * This stores the object of Service class
     * @var object
     */
    protected $service;

    /**
     * This stores the object of ApiController class
     * @var [type]
     */
    protected $api;

    /**
     * [$usersTransformer description]
     * @var [type]
     */
    protected $transformer;

    /**
     * This constructor injects the dependencies of the class
     * @param UserService $service  
     * @param APIResponse $api
     */
    public function __construct(UserService $service, APIResponse $api, UsersTransformer $transformer){
        $this->service = $service;
        $this->api = $api;
        $this->transformer = $transformer;
    }

	/**
	 * This function stores a newly created resource(User in this application) in database.
	 *
	 * @return Response
	 */
	public function register()
	{
		try{
        	$this->service->make(Input::all());
        }catch(ValidationException $e){
        	return $this->api->respondInvalidParameters($e->getMessage());
        }
        return $this->api->respondSuccessWithDetails(
        	'User has been successfully registered.', $this->transformer->transform($this->service->getUserDetails())
        );
	}

	/**
	 * This function logs in user into the system
	 * @return Response
	 */
	public function login()
	{
		try{
        	$this->service->login(Input::all());
        }catch(ValidationException $e){
        	return $this->api->respondInvalidParameters($e->getMessage());
        }
        return $this->api->respondSuccessWithDetails(
        	'Successfully logged in.', $this->transformer->transform($this->service->getUserDetails())
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
				return $this->api->respondSuccess('Successfully logged out.');
			}
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * This function updates the password of a user
	 * @return Response
	 */
	public function update()
	{
		if(($token = $this->checkTokenAndUsernameExists(Input::get('token'), Input::get('username'))) != false){
			try{
				$this->service->update(Input::all());
			}catch(ValidationException $e){
				return $this->api->respondInvalidParameters($e->getMessage());
			}
			return $this->api->respondSuccess('Password successfully changed.');
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * This function generate a new password and sends it to user' email along with an activation link
	 * @return Response
	 */
	public function forgotPassword()
	{
		try{
			if(!$this->service->forgotPassword(Input::all())){
				return $this->api->respondInvalidParameters(['Email does not exist.']);
			}
		}catch(ValidationException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
		return $this->api->respondSuccess('Check your email for new password.');
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
			$this->service->recover(Input::all(), $code);
		}catch(ValidationException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
		return $this->api->respondSuccess('Your account has been recovered. Sign in with your new password');
	}
}