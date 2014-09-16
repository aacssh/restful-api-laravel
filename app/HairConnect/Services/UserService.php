<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

class UserService{

	/**
	 * [$validator description]
	 * @var [type]
	 */
	protected $validator;

	protected $registorRules = [
		'name'              =>  'required',
        'username' 			=>  'required|max:20|min:2|unique:users',
        'password' 			=>  'required|min:6',
        'confirm_password' 	=>  'required|same:password',
        'email' 			=>  'required|max:60|email|unique:users',
        'type'				=>  'required'
	];

	protected $loginRules = [
		'email' 	=> 'required|max:50|email',
		'password' 	=> 'required|min:8'
	];

	protected $recoverRules = [
		'email' => 'required|email'
	];

	protected $updateRules = [
		'old_password'		=> 'required',
		'new_password'		=> 'required|min:6',
		'confirm_password'	=> 'required|same:new_password'
	];

	/**
	 * [$accessToken description]
	 * @var [type]
	 */
	protected $accessToken;

	function __construct(Validator $validator){
		$this->validator = $validator;
	}

	public function make(array $attributes){
		// Validate data
		if($this->validator->isValid($attributes, $this->registorRules)){
			\DB::transaction(function() use ($attributes){
				$user 			= new \User;
				$user->email 	= $attributes['email'];
				$user->username = $attributes['username'];
				$user->password = \Hash::make($attributes['password']);
				$user->save();
		
				if($attributes['type'] == 'barber'){
					$type = new \Barber;
				}else if($attributes['type'] == 'client'){
					$type = new \Client;
				}
				$name = explode(' ', $attributes['name']);
				$type->user_id 	= $user->id;
				$type->fname 	= trim($name[0]);
				$type->lname 	= trim($name[1]);
				
				if(!$type->save()){
				    throw new \Exception('User not created for account');
				}
			});
			return true;
		}
		throw new ValidationException('User\'s details validation failed', $this->validator->getErrors());
	}

	public function login(array $attributes)
	{
		if($this->validator->isValid($attributes, $this->loginRules)){
			$auth = \Auth::validate([
				'email'		=>	$attributes['email'],
				'password'	=>	$attributes['password']
			]);
			
			if($auth){
				$accessToken = str_random(60);
				$saveToken = \User::findByEmailOrFail($attributes['email']);
				$saveToken->access_token = $accessToken;

				if($saveToken->save()){
					$this->accessToken = $accessToken;
					return true;
				}
			}

			return false;
		}
		throw new ValidationException('Login validation failed', $this->validator->getErrors());
	}

	public function update(array $attributes)
	{
		if($this->validator->isValid($attributes, $this->updateRules)){
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
		throw new ValidationException('Valdation failed.', $this->validator->getErrors());
	}

	public function forgotPassword(array $attributes)
	{
		if($this->validator->isValid($attributes, $this->recoverRules)){
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
			return false;
		}
		throw new ValidationException('Password couldn\'t be reset. Please try again.', $this->validator->getErrors());
	}

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
		return false;
	}

	public function getToken()
	{
		return $this->accessToken;
	}
}