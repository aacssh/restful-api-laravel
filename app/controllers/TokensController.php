<?php
abstract class TokensController extends \BaseController{	
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
		if(!$this->isNull($token)){
			if(($user = User::findByTokenAndUsernameOrFail($token, $username)) != false){
				return $user;
			}
		}
		return false;
	}

	/**
	 * This function checks if token is null or not
	 * @param  string  $token
	 * @return boolean       
	 */
	protected function isNull($token)
	{
		if(is_null($token)){
			return true;
		}
		return false;
	}
}