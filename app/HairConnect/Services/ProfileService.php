<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

/**
 * Class ProfileService
 * @package HairConnect\Services
 */
class ProfileService{

	/**
	 * Store the object of Validator class
	 * @var object
	 */
	protected $validator;

	/**
	 * Validation rules for user profile
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
	 * Stores profile information
	 * @var object
	 */
	private $profileDetails;

	/**
	 * Construct profile service
	 * @param Validator $validator
	 */
	function __construct(Validator $validator){
		$this->validator = $validator;
	}

	/**
	 * Saves user information into the database
	 * @param  string $username  
	 * @param  array  $attributes
	 * @return boolean
	 */
	private function save($username, array $attributes)
	{
		$profile	=	\User::findByUsernameOrFail($username);

		if($profile->count()){
			$profile->fname 	  = 	$attributes['fname'];
			$profile->lname 	  = 	$attributes['lname'];
			$profile->contact_no  = 	$attributes['contact_no'];
			$profile->address 	  =	$attributes['city'].', '.$attributes['state'];
			$profile->email 	  =	$attributes['email'];
			$profile->save();
			$this->profileDetails =  $profile;
			return true;
		}
		return false;
	}

	/**
	 * Updates the profile's data
	 * @param  string $username  
	 * @param  array  $attributes
	 * @return object
	 */
	public function update($username, array $attributes)
	{
		if($this->validator->isValid($attributes, $this->rules)){
			if($this->save($username, $attributes)){
				return $this->profileDetails;
			}
		}
		throw new ValidationException('Email already exists.');
	}
}