<?php
use \HairConnect\Transformers\AppointmentsTransformer;
use \HairConnect\Services\AppointmentService;
use \HairConnect\Exceptions\ValidationException;

class ClientsAppointmentsController extends AppointmentsController{

	/**
	 * Primary user type of the controller
	 * @var string
	 */
	protected $mainUserType = 'client';

	/**
	 * Secondary user type of the controller
	 * @var string
	 */
	protected $secUserType = 'barber';

	/**
	 * Stores the object of AppointmentService class
	 * @var object
	 */
	protected $service;

	/**
	 * Prepare the object of the controller for use
	 * @param AppointmentsTransformer $appointmentsTransformer
	 * @param APIController           $api
	 * @param AppointmentService      $service
	 */
	function __construct(AppointmentsTransformer $transformer, APIResponse $api, AppointmentService $service){
		parent::__construct($transformer, $api);
		$this->service = $service;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($username)
	{	
		if($this->checkTokenAndUsernameExists(Input::get('token'), $username) != false){
			try{
				if($this->service->make($username, Input::all())){
					return $this->api->respondSuccess('Appointment has been booked in your name.');
				}
				return $this->api->respondNotFound('Appointment cannot be saved. Are you a client?');
			}catch(ValidationException $e){
				return $this->api->respondInvalidParameters($e->getErrors());
			}
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}
}