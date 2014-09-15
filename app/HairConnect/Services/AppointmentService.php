<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

class AppointmentService{

	/**
	 * [$validator description]
	 * @var [type]
	 */
	protected $validator;

	/**
	 * [$appointmentDetails description]
	 * @var [type]
	 */
	private $appointmentDetails;

	/**
	 * [$rules description]
	 * @var [type]
	 */
	protected $rules = [
		'barber_id'    =>	'required',
		'time'		   =>	'required',
		'date'		   =>	'required'
	];

	/**
	 * [__construct description]
	 * @param AppointmentValidator $validator [description]
	 */
	function __construct(Validator $validator){
		$this->validator = $validator;
	}

	/**
	 * [save description]
	 * @param  [type] $username      [description]
	 * @param  array  $attributes    [description]
	 * @param  [type] $appointmentId [description]
	 * @return [type]                [description]
	 */
	public function save($username, array $attributes)
	{
		$client = \User::findByUsernameOrFail($username)->client;

		if($client->count()){
			$date =	\Date::where('date', '=', $attributes['date'])->get()->first();
			
			$appointment = new \Appointment;
			$appointment->barber_id = $attributes['barber_id'];
			dd($attributes['barber_id']);
			$appointment->time 	    = $attributes['time'].':00';
			$appointment->client_id = $client->id;
			$appointment->deleted 	= 0;
			$appointment->date_id 	= $date->id;
			$appointment->save();

			$this->appointmentDetails = $appointment;
			return true;
		}
		return false;
	}

	/**
	 * [make description]
	 * @param  [type] $username   [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function make($username, array $attributes){
		if($this->validator->isValid($attributes, $this->rules)){
			return $this->save($username, $attributes);
		}
		throw new ValidationException('Appointment validation failed.', $this->validator->getErrors());
	}
}