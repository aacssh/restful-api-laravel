<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

/**
 * Class ShiftCreatorService
 * @package HairConnect\Services
 */
class ShiftCreatorService{

	/**
	 * Store the object of Validator class
	 * @var object
	 */
	protected $validator;

	/**
	 * Validation rules for shift
	 * @var array
	 */
	protected $rules =	[
		'start_time'    =>  'required',
        'end_time' 		=>  'required',
        'time_gap' 		=>  'required',
        'date' 			=>  'required'
	];

	/**
	 * Stores shift information
	 * @var object
	 */
	private $shiftDetails;

	/**
	 * Construct service
	 * @param Validator $validator
	 */
	function __construct(Validator $validator){
		$this->validator = $validator;
	}

	/**
	 * Saves shift's data into database
	 * @param  string $username  
	 * @param  array  $attributes
	 * @param  int $shiftId   
	 * @return boolean           
	 */
	private function save($username, array $attributes, $shiftId = null){
		$barber = \User::findByUsernameOrFail($username);
    	$date =	\Date::where('date', '=', $attributes['date'])->get();

    	if($shiftId == null){
			$shift 	=	new \Shift;    		
    	}else{
    		$shift 	=	\Shift::find($shiftId);	
    	}

		$shift->user_id	=	$barber->id;
		$shift->start_time 	=	$attributes['start_time'].':00:00';
		$shift->end_time	=	$attributes['end_time'].':00:00';
		$shift->time_gap	=	(int)$attributes['time_gap'];
		$shift->date_id 	=	$date->first()->id;
		$shift->save();
		$this->shiftDetails = 	$shift;
		return true;
	}

	/**
	 * Makes a new shift for barber
	 * @param  string $username  
	 * @param  array  $attributes
	 * @return boolean       
	 */
	public function make($username, array $attributes)
	{
		// Validate data
		if($this->validator->isValid($attributes, $this->rules)){
			return $this->save($username, $attributes);
		}
		throw new ValidationException('Invalid arguments passed.');
	}

	/**
	 * Updates shift's data
	 * @param  string $username
	 * @param  int $shiftId 
	 * @param  array  $attributes
	 * @return object
	 */
	public function update($username, $shiftId, array $attributes)
	{
		// Validate data
		if($this->validator->isValid($attributes, $this->rules)){
			$this->save($username, $attributes, $shiftId);
			return $this->shiftDetails;
		}
		throw new ValidationException('Invalid arguments passed.');
	}
}