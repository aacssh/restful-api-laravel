<?php
namespace HairConnect\Services;
use HairConnect\Validators\ShiftValidation;
use HairConnect\Exceptions\ValidationException;
use User, Shift, RuntimeException;

/**
 * Class ShiftCreatorService
 * @package HairConnect\Services
 */
class ShiftService{

	/**
	 * Store the object of Validator class
	 * @var object
	 */
	protected $validator;
	protected $user;
	protected $shift;

	/**
	 * Stores shift information
	 * @var object
	 */
	private $shiftDetails;

	/**
	 * Construct service
	 * @param Validator $validator
	 */
	function __construct(ShiftValidation $validator, User $user, Shift $shift){
		$this->validator = $validator;
		$this->user = $user;
		$this->shift = $shift;
	}

	public function getAll(array $attributes, $username){
		try{
			$this->validator->validateTokenAndUsername($this->mergeArray($attributes, ['username' => $username]));
			$user = $this->user->findByTokenAndUsernameOrFail($attributes['token'], $username);
			$this->shiftDetails = $this->shift->findAllByBarberId($user->id);
		}catch(NotFoundException $e){
			throw new RuntimeException($e->getMessage());
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}
	}

	public function show(array $attributes, $username, $shiftId){
		try{
			$this->validator->validateTokenAndUsername($this->mergeArray($attributes, ['username' => $username]));
			$this->shiftDetails = $this->shift->find($shiftId);
		}catch(NotFoundException $e){
			throw new RuntimeException($e->getMessage());
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}	
	}

	/**
	 * Saves shift's data into database
	 * @param  string $username  
	 * @param  array  $attributes
	 * @param  int $shiftId   
	 * @return boolean           
	 */
	private function save($username, array $attributes, $shiftId = null){
		$barber = $this->user->findByUsernameOrFail($username);
  	$date =	\Date::where('date', '=', $attributes['date'])->get();

  	if(!is_null($shiftId)){
  		$this->shift = $this->shift->find($shiftId);	
  	}

		$this->shift->user_id	= $barber->id;
		$this->shift->start_time = $attributes['start_time'].':00:00';
		$this->shift->end_time = $attributes['end_time'].':00:00';
		$this->shift->time_gap = (int)$attributes['time_gap'];
		$this->shift->date_id = $date->first()->id;
		$this->shift->save();
		$this->shiftDetails = $this->shift;
	}

	/**
	 * Makes a new shift for barber
	 * @param  string $username  
	 * @param  array  $attributes
	 * @return boolean       
	 */
	public function make(array $attributes, $username){
		// Validate data
		try{
			$this->validator->validateShiftAttributes($attributes);	
			$this->save($username, $attributes);
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}
	}

	/**
	 * Updates shift's data
	 * @param  string $username
	 * @param  int $shiftId 
	 * @param  array  $attributes
	 * @return object
	 */
	public function update(array $attributes, $username, $shiftId){
		try{
			$this->validator->validateShiftAttributes($attributes);	
			$this->save($username, $attributes, $shiftId);
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}
	}

	public function mergeArray($firstArray, $secondArray){
		return array_merge($firstArray, $secondArray);
	}

	public function getShiftDetails(){
		return $this->shiftDetails->toArray();
	}
}