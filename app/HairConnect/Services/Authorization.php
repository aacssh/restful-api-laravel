<?php
namespace HairConnect\Services;
use HairConnect\Exceptions\NotFoundException;
use Auth;

class Authorization{

	public function authorizeWithEmailAndPassword(array $attributes){
		$auth = Auth::validate([
			'email'	=> $attributes['email'],
			'password' => $attributes['password']
		]);

		if($auth){
			return true;
		}
		throw new NotFoundException('Given credentials does not match with any users.');
	}
}