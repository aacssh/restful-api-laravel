<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

class UserService{

	/**
	 * Store the object of Validator class
	 * @var object
	 */
	protected $validator;

	/**
	 * Validation rules for registration
	 * @var array
	 */
	protected $registorRules = [
		'name'              =>  'required',
        'username' 			=>  'required|max:20|min:2|unique:users',
        'password' 			=>  'required|min:6',
        'confirm_password' 	=>  'required|same:password',
        'email' 			=>  'required|max:60|email|unique:users',
        'type'				=>  'required'
	];

	/**
	 * Validation rules for login
	 * @var array
	 */
	protected $loginRules = [
		'email' 	=> 'required|max:50|email',
		'password' 	=> 'required|min:8'
	];

	/**
	 * Validation rules for password recovery
	 * @var array
	 */
	protected $passwordRecoveryRules = [
		'email' => 'required|email'
	];

	/**
	 * Validation rules for password update
	 * @var array
	 */
	protected $passwordUpdateRules = [
		'old_password'		=> 'required',
		'new_password'		=> 'required|min:6',
		'confirm_password'	=> 'required|same:new_password'
	];

	/**
	 * Stores user details
	 * @var object
	 */
	protected $userDetails;

	/**
	 * Construct user service
	 * @param Validator $validator
	 */
	function __construct(Validator $validator){
		$this->validator = $validator;
	}

	/**
	 * Makes a new user
	 * @param  array  $attributes
	 * @return boolean
	 */
	public function make(array $attributes){
		if($this->validator->isValid($attributes, $this->registorRules)){
			$name = explode(' ', $attributes['name'], 2);
			$accessToken = bin2hex(mcrypt_create_iv(15, MCRYPT_DEV_URANDOM));
			$user 			= new \User;
			$user->email 	= $attributes['email'];
			$user->username = $attributes['username'];
			$user->password = \Hash::make($attributes['password']);
			$user->type     = $attributes['type'];
			$user->fname 	= trim($name[0]);
			$user->lname 	= trim($name[1]);
			$user->deactivated = 0;
			$user->online 	= 1;
			$user->access_token = $accessToken;
			
			if(!$user->save()){
			    throw new \Exception('User accountnot created.');
			}

			$this->userDetails = $user;
			return true;
		}
		throw new ValidationException('Username or email is already taken.');
	}

	/**
	 * Logs in user
	 * @param  array  $attributes
	 * @return boolean
	 */
	public function login(array $attributes)
	{
		if($this->validator->isValid($attributes, $this->loginRules)){
			$auth = \Auth::validate([
				'email'		=>	$attributes['email'],
				'password'	=>	$attributes['password']
			]);

			
			if($auth){
				$accessToken = bin2hex(mcrypt_create_iv(15, MCRYPT_DEV_URANDOM));
				$user = \User::findByEmailOrFail($attributes['email']);
				$user->access_token = $accessToken;

				if($user->save()){
					$this->accessToken = $accessToken;
					$this->userDetails = $user;
					return true;
				}
			}
		}
		throw new ValidationException('Password or email does not match.');
	}

	/**
	 * Updates user's password
	 * @param  array  $attributes 
	 * @return boolean    
	 */
	public function update(array $attributes)
	{
		if($this->validator->isValid($attributes, $this->passwordUpdateRules)){
			$user = \User::find(\Auth::user()->id);

			// Check if the given old password is correct or not
			if(\Hash::check($attributes['old_password'], $user->getAuthPassword())){
				$user->password = \Hash::make($attributes['new_password']);
				if($user->save()){
					return true;
				}
			}
			return false;
		}
		throw new ValidationException('Old password is invalid.');
	}

	/**
	 * Creates new password and sends a recover link in an email
	 * @param  array  $attributes
	 * @return boolean
	 */
	public function forgotPassword(array $attributes)
	{
		if($this->validator->isValid($attributes, $this->passwordRecoveryRules)){
			$user = \User::where('email', '=', $attributes['email']);

			if($user->count()){
				$code 					= str_random(60);
				$password 				= str_random(10);
				$user 					= $user->first();
				$user->code 			= $code;
				$user->password_temp 	= \Hash::make($password);

				if($user->save()){
					\Mail::send('emails.auth.forgot', ['link' => \URL::route('api.v1.users.recover', $code), 'username' => $user->username, 'password' => $password], function($message) use ($user){
						$message->to($user->email, $user->username)->subject('Your new password');
					});
				}
				return true;
			}
		}
		throw new ValidationException('Email does not exist.');
	}

	/**
	 * Recovers user's password when users clicks password recovery link sent in an email by reseting old password with new password
	 * @param  array  $attributes
	 * @param  string $code      
	 * @return boolean
	 */
	public function recover(array $attributes, $code)
	{
		$user = \User::where('code', '=', $code)->where('password_temp', '!=', '');

		if($user->count()){
			$user 					= $user->first();
			$user->password 		= $user->password_temp;
			$user->password_temp 	= '';
			$user->code 		 	= '';
			
			if($user->save()){
				return true;	
			}
		}
		throw new ValidationException('Something is wrong with this link.');
	}

	public function getUserDetails()
	{
		return $this->userDetails;
	}
}