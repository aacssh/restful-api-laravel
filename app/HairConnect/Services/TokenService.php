<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Exceptions\ValidationException;

class TokenService{
	/**
	 * This variable stores user's access token
	 * @var string
	 */
	protected $token;

	/**
	 * Stores a message for any invald token
	 * @var constant
	 */
	const MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME = 'Invalid token or User not found';

	/**
	 * This function checks if token and username exists in the database.
	 * Token and username must be of a same user.
	 * @param  string $token
	 * @param  string $username
	 * @return mixed          
	 */
	public function checkTokenAndUsernameExists($token, $username)
	{
		if(!is_null($token)){
			if(($user = User::findByTokenAndUsernameOrFail($token, $username)) != false){
				return $user;
			}
		}
		return false;
	}
}