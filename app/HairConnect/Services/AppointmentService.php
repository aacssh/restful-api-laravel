<?php
namespace HairConnect\Services;
use HairConnect\Validators\Validator;
use HairConnect\Validators\ValidationException;

/**
 * Class AppointmentService
 * @package HairConnect\Services
 */
class AppointmentService{

	/**
	 * Store the object of Validator class
	 * @var object
	 */
	protected $validator;

	/**
	 * Stores the data of appointment
	 * @var object
	 */
	private $appointmentDetails;

	/**
	 * Validation rules for appointment
	 * @var [type]
	 */
	protected $rules = [
		'barber_id'    =>	'required',
		'time'		   =>	'required',
		'date'		   =>	'required'
	];

	/**
	 * Construct the appointment service
	 * @param AppointmentValidator $validator
	 */
	function __construct(Validator $validator){
		$this->validator = $validator;
	}

	/**
	 * Saves appointment data into database
	 * @param  string $username      
	 * @param  array  $attributes
	 * @return boolean               
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
	 * Makes a new appointment
	 * @param  string $username  
	 * @param  array  $attributes
	 * @return boolean           
	 */
	public function make($username, array $attributes){
		if($this->validator->isValid($attributes, $this->rules)){
			return $this->save($username, $attributes);
		}
		throw new ValidationException('Invalid arguments passed');
	}
}