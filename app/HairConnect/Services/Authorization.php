<?php
namespace HairConnect\Services;
use HairConnect\Exceptions\NotFoundException;
use Auth, User;

class Authorization{

	protected $user;

	function __construct(User $user){
		$this->user = $user;
	}

	public function authorizeWithEmailAndPassword(array $attributes){
		try{
			$this->validate($attributes);
		}catch(NotFoundException $e){
				throw new NotFoundException('Given credentials does not match with any users.');		
		}
	}

	private function validate(array $attributes){
		$auth = Auth::validate([
			'email'	=> $attributes['email'],
			'password' => $attributes['password']
		]);

		if(!$auth){
			throw new NotFoundException('Given credentials does not match with any users.');
		}
	}
}