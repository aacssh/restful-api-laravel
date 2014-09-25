<?php
namespace HairConnect\Services;
use HairConnect\Validators\AppointValidation;
use HairConnect\Exceptions\ValidationException;

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
	 * Construct the appointment service
	 * @param AppointmentValidator $validator
	 */
	function __construct(AppointmentValidation $validator){
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
			$appointment->time = $attributes['time'].':00';
			$appointment->client_id = $client->id;
			$appointment->deleted = 0;
			$appointment->date_id = $date->id;
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
		if($this->validator->validateAppointmentAttribtes($attributes){
			return $this->save($username, $attributes);
		}
		throw new ValidationException('Invalid arguments passed');
	}
}