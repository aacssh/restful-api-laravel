<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

class BarberService{

	/**
	 * [$validator description]
	 * @var [type]
	 */
	protected $validator;

	/**
	 * [$rules description]
	 * @var array
	 */
	protected $rules = [
		'fname'			=> 	'required|Alpha',
		'lname'			=>	'required|Alpha',
		'contact_no'	=>	'required|numeric',
		'address'		=>	'required',
		'email'			=>	'required|email'
	];

	/**
	 * [$barber description]
	 * @var object
	 */
	private $barberDetails;

	/**
	 * [__construct description]
	 * @param BarberValidator $barberValidator [description]
	 */
	function __construct(Validator $validator){
		$this->validator = $validator;
	}

	/**
	 * `
	 * @param  [type] $username   [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	private function save($username, array $attributes)
	{
		$user	=	\User::findByUsernameOrFail($username);
		$barber =	$user->barber;

		if($barber->count()){
			$barber->fname 		 = 	$attributes['fname'];
			$barber->lname 		 = 	$attributes['lname'];
			$barber->contact_no  = 	$attributes['contact_no'];
			$barber->address 	 =	$attributes['address'];
			$barber->save();

			$user->email 		 =	$attributes['email'];
			$user->save();
			$this->barberDetails =  $barber;
			return true;
		}
		return false;
	}

	/**
	 * [update description]
	 * @param  [type] $username   [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function update($username, array $attributes)
	{
		if($this->validator->isValid($attributes, $this->rules)){
			if($this->save($username, $attributes)){
				return $this->barberDetails;
			}
		}
		throw new ValidationException('Barber validation failed', $this->validator->getErrors());
	}
}