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
		try{
			$this->service->make($username, Input::all());
			return $this->api->respondSuccess('Appointment has been booked in your name.');
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
	}
}