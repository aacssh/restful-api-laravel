<?php
use \HairConnect\Transformers\AppointmentsTransformer;
use \HairConnect\Services\AppointmentService;
use \HairConnect\Validators\ValidationException;

class ClientsAppointmentsController extends AppointmentsController{

	/**
	 * [$mainClass description]
	 * @var [type]
	 */
	protected $mainClass = 'client';

	/**
	 * [$secClass description]
	 * @var [type]
	 */
	protected $secClass = 'barber';

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
	 * [getMainUser description]
	 * @param  [type] $username [description]
	 * @return [type]           [description]
	 */
	public function getMainUser($username){
		return User::findByUsernameOrFail($username)->client;
	}

	/**
	 * [getSecondaryUser description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getSecondaryUser($id){
		return Barber::find($id);
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($username)
	{	
		if($this->checkToken(Input::get('token'), $username)){
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
			'error' => [			
            	'message' => 'Invalid token'
            ]
        ]);
	}
}