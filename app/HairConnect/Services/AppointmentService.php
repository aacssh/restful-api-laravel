<?php
namespace HairConnect\Services;
use HairConnect\Validators\AppointmentValidation;
use HairConnect\Exceptions\ValidationException;
use User;

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
	protected $user;

	/**
	 * Stores the data of appointment
	 * @var object
	 */
	private $appointmentDetails;

	/**
	 * Construct the appointment service
	 * @param AppointmentValidator $validator
	 */
	function __construct(AppointmentValidation $validator, User $user){
		$this->validator = $validator;
		$this->user = $user;
	}

	/**
	 * Saves appointment data into database
	 * @param  string $username      
	 * @param  array  $attributes
	 * @return boolean               
	 */
	public function save($username, array $attributes)
	{
		$client = $this->user->findByUsernameOrFail($username);
		$date =	\Date::where('date', '=', $attributes['date'])->get()->first();			
		$appointment = new \Appointment;
		$appointment->barber_id = $attributes['barber_id'];
		dd($attributes['barber_id']);
		$appointment->time = $attributes['time'].':00';
		$appointment->client_id = $client->id;
		$appointment->deleted = 0;
		$appointment->date_id = $date->id;
		if(!$appointment->save()){
			throw new NotSavedException('Appointment cannot be saved');
		}
		$this->appointmentDetails = $appointment;
	}

	/**
	 * Makes a new appointment
	 * @param  string $username  
	 * @param  array  $attributes
	 * @return boolean           
	 */
	public function make($username, array $attributes){
		try{
			$this->validator->validateAppointmentAttribtes($attributes);
			$this->save($username, $attributes);
		}catch(NotSavedException $e){
			throw new RuntimeException($e->getMessage());
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}
	}
}