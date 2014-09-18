<?php

class TokensController extends \BaseController{
	
	/**
	 * [$token description]
	 * @var [type]
	 */
	protected $token;

	public function checkToken($token, $username)
	{
		if(!$this->isNull($token)){
			if(($user = User::findByTokenAndUsernameOrFail($token, $username)) != false){
				return $user;
			}
		}
		return false;
	}

	public function isNull($token)
	{
		if(is_null($token)){
			return true;
		}
		return false;
	}
}