<?php
use HairConnect\Exceptions\NotFoundException;

abstract class TokensController extends \BaseController{	
	/**
	 * This variable stores user's access token
	 * @var string
	 */
	protected $token;
	protected $user;

	function __construct(User $user){
		$this->user = $user;
	}

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
		try{
			return $this->user->findByTokenAndUsernameOrFail($token, $username);
		}catch(NotFoundException $e){
			throw new NotFoundException($e);
		}
	}
}