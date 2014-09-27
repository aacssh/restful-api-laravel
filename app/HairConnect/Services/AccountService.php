<?php
namespace HairConnect\Services;
use HairConnect\Services\Authorization;
use HairConnect\Validators\AccountValidation;
use HairConnect\Exceptions\ValidationException;
use HairConnect\Exceptions\NotSavedException;
use HairConnect\Exceptions\NotFoundException;
use User, Hash, Mail, URL, Auth, Exception;

class AccountService{

	/**
	 * @var object
	 */
	protected $user;
	protected $auth;
	protected $userDetails;
	protected $validator;

	/**
	 * Construct user service
	 * @param Validator $validator
	 */
	function __construct(User $user, AccountValidation $validator, Authorization $auth){
		$this->user = $user;
		$this->validator = $validator;
		$this->auth = $auth;
	}

	/**
	 * Makes a new user
	 * @param  array  $attributes
	 * @return boolean
	 */
	public function make(array $attributes){
		try{
			$this->validator->validateRegisterAttributes($attributes);
			$name = explode(' ', $attributes['name'], 2);
			$this->user->email = $attributes['email'];
			$this->user->username = $attributes['username'];
			$this->user->password = Hash::make($attributes['password']);
			$this->user->type = $attributes['type'];
			$this->user->fname = trim($name[0]);
			$this->user->lname = trim($name[1]);
			$this->user->deactivated = 0;
			$this->user->online = 1;
			$this->user->access_token = $this->makeAccessToken();
			
			if($this->user->save()){
			  $this->userDetails = $this->user;
				return true;
			}
			throw new NotSavedException("User Cannot be saved");
		}catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}

	/**
	 * Logs in user
	 * @param  array  $attributes
	 * @return boolean
	 */
	public function login(array $attributes){
		try{
			$this->validator->validateLoginAttributes($attributes);
			$this->auth->authorizeWithEmailAndPassword($attributes);
			$user = $this->user->findByEmailOrFail($attributes['email']);
			$user->access_token = $this->makeAccessToken();

			if($user->save()){
				$this->accessToken = $user->access_token;
				$this->userDetails = $user;
				return true;
			}
			throw new NotSavedException("User Cannot be saved");
		}catch(NotFoundException $e){
	  	throw new ValidationException($e->getMessage());
	  }catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}

	/**
	 * Updates user's password
	 * @param  array  $attributes 
	 * @return boolean    
	 */
	public function update(array $attributes){
		try{
			$this->validator->validatePasswordUpdateAttributes($attributes);
			$user = $this->user->findByUsernameOrFail($attributes['username']);

			// Check if the given old password is correct or not
			if(Hash::check($attributes['old_password'], $user->getAuthPassword())){
				$user->password = Hash::make($attributes['new_password']);
				if($user->save()){
					$this->userDetails = $user;
					return true;
				}
			}
			throw new NotSavedException("User Cannot be saved");
		}catch(NotFoundException $e){
	  	throw new ValidationException($e->getMessage());  	
	  }catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}

	/**
	 * Creates new password and sends a recover link in an email
	 * @param  array  $attributes
	 * @return boolean
	 */
	public function forgotPassword(array $attributes){
		try{
			$this->validator->validatePasswordRecoveryAttributes($attributes);
			$user =$this->user->findByEmailOrFail($attributes['email']);
			$code = str_random(60);
			$password = str_random(10);
			$user = $user->first();
			$user->code	= $code;
			$user->password_temp = Hash::make($password);
			if($user->save()){
				Mail::send('emails.auth.forgot', ['link' => URL::route('api.v1.users.recover', $code), 'username' => $user->username, 'password' => $password], function($message) use ($user){
					$message->to($user->email, $user->username)->subject('Your new password');
				});
				return true;
			}
			throw new NotSavedException('Emai cannot be sent.');
		}catch(NotFoundException $e){
	  	throw new ValidationException('Email does not exist.'); 
	  }catch(ValidationException $e){
			throw new ValidationException('Email does not exist.');
		}
	}

	public function destroy(array $attributes){
		try{
			$this->validator->validateTokenAndUsername($attributes);
			$user = $this->user->findByTokenAndUsernameOrFail($attributes['token'], $attributes['username']);
			$user->access_token = NULL;
			if(!$user->save()){
				throw new NotSavedException();
			}
		}catch(NotFoundException $e){
			throw new ValidationException($e->getMessage());
		}catch(ValidationException $e){
			throw new ValidationException($e->getMessage());
		}
	}

	/**
	 * Recovers user's password when users clicks password recovery link sent in an email by reseting old password with new password
	 * @param  array  $attributes
	 * @param  string $code      
	 * @return boolean
	 */
	public function recover(array $attributes, $code){
		try{
			$user = $this->user->findByRecoveryCode($code);
			$user->password = $user->password_temp;
			$user->password_temp = '';
			$user->code	= '';
			
			if($user->save()){
				return true;	
			}
		}catch(NotFoundException $e){
			throw new NotFoundException($e);
		}
		throw new ValidationException('Something is wrong with this link.');
	}

	public function getUserDetails(){
		return $this->userDetails;
	}

	private function makeAccessToken(){
		return bin2hex(mcrypt_create_iv(15, MCRYPT_DEV_URANDOM));
	}
}