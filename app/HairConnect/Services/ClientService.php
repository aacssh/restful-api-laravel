<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

class ClientService{

	/**
	 * [$validator description]
	 * @var [type]
	 */
	protected $validator;

	/**
	 * [$rules description]
	 * @var [type]
	 */
	protected $rules = [
		'fname'			=> 	'required|Alpha',
		'lname'			=>	'required|Alpha',
		'contact_no'	=>	'required|numeric',
		'address'		=>	'required',
		'email'			=>	'required|email'	
	];

	/**
	 * [$clientDetails description]
	 * @var object
	 */
	private $clientDetails;

	/**
	 * [__construct description]
	 * @param ClientValidator $validator [description]
	 */
	function __construct(Validator $validator){
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
		if($this->validator->isValid($attributes, $this->rules)){
			if($this->save($username, $attributes)){
				return $this->clientDetails;
			}	
		}
		throw new ValidationException('Client validation failed', $this->validator->getErrors());
	}
}