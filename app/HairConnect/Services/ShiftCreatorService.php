<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

class ShiftCreatorService{

	/**
	 * [$validator description]
	 * @var [type]
	 */
	protected $validator;

	/**
	 * [$rules description]
	 * @var array
	 */
	protected $rules =	[
		'start_time'    =>  'required',
        'end_time' 		=>  'required',
        'time_gap' 		=>  'required',
        'date' 			=>  'required'
	];

	/**
	 * [$shift description]
	 * @var [type]
	 */
	private $shiftDetails;

	/**
	 * [__construct description]
	 * @param ShiftValidator $validator [description]
	 */
	function __construct(Validator $validator){
		$this->validator = $validator;
	}

	/**
	 * [save description]
	 * @param  [type] $username   [description]
	 * @param  array  $attributes [description]
	 * @param  [type] $shiftId    [description]
	 * @return [type]             [description]
	 */
	private function save($username, array $attributes, $shiftId = null){
		$barber = \User::findByUsernameOrFail($username)->barber;
    	$date =	\Date::where('date', '=', $attributes['date'])->get();

    	if($shiftId == null){
			$shift 	=	new \Shift;    		
    	}else{
    		$shift 	=	\Shift::find($shiftId);	
    	}

		$shift->barber_id	=	$barber->id;
		$shift->start_time 	=	$attributes['start_time'].':00:00';
		$shift->end_time	=	$attributes['end_time'].':00:00';
		$shift->time_gap	=	(int)$attributes['time_gap'];
		$shift->date_id 	=	$date->first()->id;
		$shift->save();
		$this->shiftDetails = 	$shift;
		return true;
	}

	public function make($username, array $attributes)
	{
		// Validate data
		if($this->validator->isValid($attributes)){
			return $this->save($username, $attributes);
		}
		throw new ValidationException('Shift validation failed', $this->validator->getErrors());
	}

	public function update($username, $shiftId, array $attributes)
	{
		// Validate data
		if($this->validator->isValid($attributes, $this->rules)){
			$this->save($username, $attributes, $shiftId);
			return $this->shiftDetails;
		}
		throw new ValidationException('Shift validation failed', $this->validator->getErrors());
	}
}