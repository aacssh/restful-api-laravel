<?php
namespace HairConnect\Services;
use HairConnect\Validators\UserValidator;
use HairConnect\Validators\LoginValidator;
use HairConnect\Validators\ValidationException;

class UserService{

	/**
	 * [$validator description]
	 * @var [type]
	 */
	protected $validator;

	/**
	 * [$loginValidator description]
	 * @var [type]
	 */
	protected $loginValidator;

	/**
	 * [$userDetais description]
	 * @var [type]
	 */
	protected $userDetais;

	function __construct(UserValidator $validator, LoginValidator $loginValidator){
		$this->validator = $validator;
		$this->loginValidator = $loginValidator;
	}

	public function save(array $attributes)
	{
		\DB::transaction(function() use ($attributes){
			//$code 	  		= str_random(60);
			// Storing user's data
			$user 			= new \User;
			$user->email 	= $attributes['email'];
			$user->username = $attributes['username'];
			$user->password = \Hash::make($attributes['password']);
			//$user->code 	= $code;
			//$user->active 	= 0;
			$user->save();
			$this->userDetails = $user;
	
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

	public function make(array $attributes){
		// Validate data
		if($this->validator->isValid($attributes)){
			if($this->save($attributes)){
				return $this->userDetails;
			}
		}
		throw new ValidationException('User\'s details validation failed', $this->validator->getErrors());
	}

	public function login(array $attributes)
	{
		if($this->loginValidator->isValid($attributes)){
			$auth = \Auth::attempt([
				'email'		=>	$attributes['email'],
				'password'	=>	$attributes['password']
			]);
			if($auth){
				return true;
			}
		}
		throw new ValidationException('Login validation failed', $this->validator->getErrors());
	}
}