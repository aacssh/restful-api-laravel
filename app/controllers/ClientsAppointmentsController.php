<?php
use \HairConnect\Transformers\AppointmentsTransformer;
use \HairConnect\Services\AppointmentService;
use \HairConnect\Validators\ValidationException;

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
	protected $appointmentService;

	/**
	 * Prepare the object of the controller for use
	 * @param AppointmentsTransformer $appointmentsTransformer
	 * @param APIController           $apiController          
	 * @param AppointmentService      $appointmentService     
	 */
	function __construct(AppointmentsTransformer $appointmentsTransformer, APIController $apiController, AppointmentService $appointmentService){
		parent::__construct($appointmentsTransformer, $apiController);
		$this->appointmentService = $appointmentService;
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
				if($this->appointmentService->make($username, Input::all())){
					return $this->apiController->respondSuccess('Appointment has been booked in your name.');
				}
				return $this->apiController->respondNotFound('Appointment cannot be saved. Are you a client?');
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getErrors());
			}
		}
		return $this->apiController->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}
}