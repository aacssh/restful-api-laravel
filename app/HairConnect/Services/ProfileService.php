<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

class ProfileService{

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
		'city'			=>	'required',
		'state'			=>	'required',
		'email'			=>	'required|email'
	];

	/**
	 * [$barber description]
	 * @var object
	 */
	private $profileDetails;

	/**
	 * [__construct description]
	 * @param ProfileValidator $profileValidator [description]
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
		$profile	=	\User::findByUsernameOrFail($username);

		if($profile->count()){
			$profile->fname 		 = 	$attributes['fname'];
			$profile->lname 		 = 	$attributes['lname'];
			$profile->contact_no  = 	$attributes['contact_no'];
			$profile->address 	 =	$attributes['city'].', '.$attributes['state'];
			$profile->email 		 =	$attributes['email'];
			$profile->save();
			$this->profileDetails =  $profile;
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
				return $this->profileDetails;
			}
		}
		throw new ValidationException('Profile validation failed', $this->validator->getErrors());
	}
}