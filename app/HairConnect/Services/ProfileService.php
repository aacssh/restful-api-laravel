<?php
namespace HairConnect\Services;
use HairConnect\Validators\ProfileValidation;
use HairConnect\Exceptions\ValidationException;
use HairConnect\Exceptions\NotFoundException;
use User, RuntimeException, HairStyleImage;

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
	protected $user;
	protected $hairStyleImage;

	/**
	 * Stores profile information
	 * @var object
	 */
	private $profileDetails;
	private $paginateDetails;

	/**
	 * Construct profile service
	 * @param Validator $validator
	 */
	function __construct(ProfileValidation $validator, User $user, HairStyleImage $hairStyleImage){
		$this->validator = $validator;
		$this->user = $user;
		$this->hairStyleImage = $hairStyleImage;
	}

	public function search(array $attributes, $userType){
		$limit = $this->getLimit($attributes);
		$searchAttributes = [
			'userType' => $userType,
			'limit' => $limit,
			'name' => $this->getName($attributes),
			'city' => $this->getCity($attributes),
			'zip' => $this->getZip($attributes)
		];
		try{
			$profiles = $this->user->findByUsersTypeAndSearchAttributes($searchAttributes);
			$this->profileDetails = $profiles;
			$this->setPaginator($profiles, $limit);
		}catch(NotFoundException $e){
			throw new RuntimeException($e->getMessage());
		}		
	}

	public function getLimit($attributes){
		if(array_key_exists('limit', $attributes)){
			return $attributes['limit'];
		}
		return 5;
	}

	public function getName($attributes){
		if(array_key_exists('name', $attributes)){
			$name = explode(' ', $attributes['name']);
			if(count($name) == 1){
				$name[1] = $name[0];
			}
			return $name;
		}
	}

	public function getCity($attributes){
		if(array_key_exists('city', $attributes)){
			return $attributes['city'];
		}
	}

	public function getZip($attributes){
		if(array_key_exists('zip', $attributes)){
			return $attributes['zip'];
		}
	}

	public function setPaginator($profile, $limit){
		$this->paginateDetails =  [
    	'total_count' => $profile->getTotal(),
    	'total_pages' => ceil($profile->getTotal()/$profile->getPerPage()),
    	'current_page' =>	$profile->getCurrentPage(),
    	'limit' => (int)$limit,
    	'prev' =>	$profile->getLastPage()
		];
	}

	public function getPaginator(){
		return $this->paginateDetails;
	}

	/**
	 * Saves user information into the database
	 * @param  string $username  
	 * @param  array  $attributes
	 * @return boolean
	 */
	private function save($username, array $attributes){
		try{
			$profile = $this->user->findByUsernameOrFail($username);
			$profile->fname = $attributes['fname'];
			$profile->lname = $attributes['lname'];
			$profile->contact_no = $attributes['contact_no'];
			$profile->address =	$attributes['city'].', '.$attributes['state'];
			$profile->email = $attributes['email'];
			if(!$profile->save()){
				throw new NotSavedException('Profile info not saved.');	
			}
			$this->profileDetails = $profile;
		}catch(NotFoundException $e){
			throw new ValidationException($e->getMessage());
		}
	}

	/**
	 * Updates the profile's data
	 * @param  string $username  
	 * @param  array  $attributes
	 * @return object
	 */
	public function update(array $attributes, $username){
		try{
			$this->validator->validateProfileAttributes($this->mergeArray($attributes, ['username' => $username]));
			$this->save($username, $attributes);
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}catch(NotSavedException $e){
			throw new RuntimeException($e->getMessage());
		}
	}

	public function show(array $attributes, $username){
		try{
			$this->validator->validateTokenAndUsername($this->mergeArray($attributes, ['username' => $username]));
			$user = $this->user->findByTokenAndUsernameOrFail($attributes['token'], $username);
			$this->setProfileDetails($user);
		}catch(NotFoundException $e){
			throw new RuntimeException($e->getMessage());
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}
	}

	public function destroy(array $attributes, $username){
		try{
			$this->validator->validateTokenAndUsername($this->mergeArray($attributes, ['username' => $username]));
			$user = $this->user->findByTokenAndUsernameOrFail($attributes['token'], $username);
			$user->online	= 0;
			$user->deactivated = 0;
			$user->save();
		}catch(NotFoundException $e){
			throw new RuntimeException($e->getMessage());
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}	
	}

	public function setProfileDetails($user){
		if($user->type == 'barber'){
				$hairStyleImages = ['hair_style_images' => $this->hairStyleImage->findAllByBarberId($user->id)->toArray()];
				$this->profileDetails = (object)$this->mergeArray($user->toArray(), $hairStyleImages);
			}else{
				$this->profileDetails = $user;
			}
	}

	public function mergeArray($firstArray, $secondArray){
		return array_merge($firstArray, $secondArray);
	}

	public function getProfileDetails(){
		return $this->profileDetails;
	}
}