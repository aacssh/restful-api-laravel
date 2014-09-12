<?php
namespace HairConnect\Services;
use HairConnect\Validators\ClientValidator;
use HairConnect\Validators\ValidationException;

class ClientService{

	/**
	 * [$validator description]
	 * @var [type]
	 */
	protected $validator;

	/**
	 * [$clientDetails description]
	 * @var object
	 */
	private $clientDetails;

	/**
	 * [__construct description]
	 * @param ClientValidator $validator [description]
	 */
	function __construct(ClientValidator $validator){
		$this->validator = $validator;
	}

	/**
	 * [save description]
	 * @param  [type] $username   [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	private function save($username, array $attributes)
	{
		$user 	 =	\User::findByUsernameOrFail($username);
		$client  =	$user->client;

		if($client->count()){
			$client->fname 		= $attributes['fname'];
			$client->lname 		= $attributes['lname'];
			$client->contact_no = $attributes['contact_no'];
			$client->address 	=	$attributes['address'];
			$client->save();

			$user->email 		=	$attributes['email'];
			$user->save();
			$this->clientDetails = $client;
			return true;
		}
		return false;
	}

	/**
	 * [upate description]
	 * @param  [type] $username   [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function update($username, array $attributes)
	{
		if($this->validator->isValid($attributes)){
			if($this->save($username, $attributes)){
				return $this->clientDetails;
			}	
		}
		throw new ValidationException('Client validation failed', $this->validator->getErrors());
	}
}