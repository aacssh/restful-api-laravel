<?php
use \HairConnect\Transformers\AppointmentsTransformer;
use \HairConnect\Services\AppointmentService;
use \HairConnect\Validators\ValidationException;

class ClientsAppointmentsController extends AppointmentsController{

	/**
	 * [$mainClassType description]
	 * @var [type]
	 */
	protected $mainType = 'client';

	/**
	 * [$secType description]
	 * @var [type]
	 */
	protected $secType = 'barber';

	/**
	 * [$appointmentService description]
	 * @var [type]
	 */
	protected $appointmentService;

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
		if($this->checkToken(Input::get('token'), $username) != false){
			try{
				if($this->appointmentService->make($username, Input::all())){
					return $this->apiController->respond([
						'message'	=>	'Appointment has been booked in your name.'
					]);	
				}
				return $this->apiController->respondNotFound('Appointment cannot be saved. Are you a client?');
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getErrors());
			}
		}
		return $this->apiController->respond([
			'errors' => [
            	'message' => 'Invalid token or User cannot be found.'
            ]
        ]);
	}
}