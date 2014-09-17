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
			if(User::findByTokenAndUsernameOrFail($token, $username) != false){
				dd(User::findByTokenAndUsernameOrFail($token, $username));
				return true;
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