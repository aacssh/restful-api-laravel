<?php
namespace HairConnect\Services;
use HairConnect\Validators\AppointmentValidation;
use HairConnect\Exceptions\ValidationException;
use HairConnect\Exceptions\NotFoundException;
use User, Appointment, Date, RuntimeException;

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
	protected $appointment;

	/**
	 * Stores the data of appointment
	 * @var object
	 */
	private $appointmentDetails;
	private $paginateDetails;
	private $mainUserType;
	private $secUserType;

	/**
	 * Construct the appointment service
	 * @param AppointmentValidator $validator
	 */
	function __construct(AppointmentValidation $validator, User $user, Appointment $appointment, Date $date){
		$this->validator = $validator;
		$this->user = $user;
		$this->appointment = $appointment;
		$this->date = $date;
	}

	public function setUsers($mainUserType, $secUserType){
		$this->mainUserType = $mainUserType;
		$this->secUserType = $secUserType;
	}

	public function showAll(array $attributes, $username){
		try{
			$this->validator->validateToken($attributes);
			$user = $this->user->findByTokenAndUsernameOrFail($attributes['token'], $username);
			$limit = $this->getLimit($attributes);
			$appointments = $this->appointment->findByUserWithPaginate("{$this->mainUserType}_id", $user->id, $limit);
			$appointmentsOfSecUserType = [];

			foreach($appointments as $appointment){
				$secUserType = $this->getSecondaryUserType($appointment);
				$date =	$this->date->find($appointment->date_id);
				array_push($appointmentsOfSecUserType, $this->buildArray($secUserType, $appointment, $date));
			}
			$this->appointmentDetails = $appointmentsOfSecUserType;
		  $this->setPaginator($appointments, $limit);
		}catch(NotFoundException $e){
			throw new RuntimeException($e->getMessage());
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}
	}

	public function getLimit($attributes){
		if(array_key_exists('limit', $attributes)){
			return $attributes['limit'];
		}
		return 5;
	}

	public function setPaginator($appointments, $limit){
		$this->paginateDetails =  [
    	'total_count' => $appointments->getTotal(),
    	'total_pages' => ceil($appointments->getTotal()/$appointments->getPerPage()),
    	'current_page' =>	$appointments->getCurrentPage(),
    	'limit' => (int)$limit,
    	'prev' =>	$appointments->getLastPage()
		];
	}

	public function getPaginator(){
		return $this->paginateDetails;
	}

	public function show(array $attributes, $username, $appointmentId){
		try{
			$this->validator->validateToken($attributes);
			$this->user->findByTokenAndUsernameOrFail($attributes['token'], $username);
			$appointment = $this->appointment->findById($appointmentId);
			$secUserType = $this->getSecondaryUserType($appointment);
			$date =	$this->date->find($appointment->date_id);
			$this->appointmentDetails = $this->buildArray($secUserType, $appointment, $date);
		}catch(NotFoundException $e){
			throw new RuntimeException($e->getMessage());
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}
	}

	public function getSecondaryUserType($appointment){
		if($this->secUserType == 'barber'){
			return $this->user->find($appointment->barber_id);
		}else if($this->secUserType == 'client'){
			return $this->user->find($appointment->client_id);
		}
	}

	public function buildArray($secUserType, $appointment, $date){
		return [
			'appointment_id' => $appointment->id,
			"{$this->secUserType}_name" => $secUserType->fname.' '.$secUserType->lname, 
			"{$this->secUserType}_username" => $secUserType->username, 
			'cancelled' => $appointment->deleted, 
			'time' => $appointment->time, 
			'date' => $date->date
		];
	}

	/**
	 * Saves appointment data into database
	 * @param  string $username      
	 * @param  array  $attributes
	 * @return boolean               
	 */
	public function save($username, array $attributes){
		$client = $this->user->findByTokenAndUsernameOrFail($attributes['token'],$username);
		$this->appointment->barber_id = $attributes['barber_id'];
		$this->appointment->time = $attributes['time'].':00';
		$this->appointment->client_id = $client->id;
		$this->appointment->deleted = 0;
		$this->appointment->date_id = $this->date->findByDate($attributes['date'])->id;
		if(!$this->appointment->save()){
			throw new NotSavedException('Appointment cannot be saved');
		}
		$this->appointmentDetails = $this->appointment;
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

	public function destroy(array $attributes, $username, $appointmentId){
		try{
			$this->validator->validateAppointmentAttribtes($attributes);
			$this->user->findByTokenAndUsernameOrFail($attributes['token'], $username);
			$this->delete($appointmentId);
		}catch(NotSavedException $e){
			throw new RuntimeException($e->getMessage());
		}catch(NotFoundException $e){
			throw new RuntimeException($e->getMessage());
		}catch(ValidationException $e){
			throw new RuntimeException($e->getMessage());
		}
	}

	public function delete($appointmentId){
		try{
			$appointment = $this->appointment->findById($appointmentId);
			$appointment->deleted = 1;
			if(!$appointment->save()){
				throw new NotSavedException('Appointment cannot be saved. Please try again later');
			}
		}catch(NotFoundException $e){
			throw new NotFoundException($e->getMessage());	
		}
	}

	public function getAppointmentDetails(){
		return $this->appointmentDetails;
	}
}