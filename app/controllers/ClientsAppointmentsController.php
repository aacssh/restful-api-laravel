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
		return Barber::where('id', '=', $id)->get()->first();
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($username)
	{	
		try{
			if($this->appointmentService->make($username, Input::all())){
				return $this->apiController->respond([
					'message'	=>	'Appointment has been booked in your name.'
				]);	
			}
			return $this->apiController->respondNotFound('Appointment cannot be saved.');
		}catch(ValidationException $e){
			return $this->apiController->respondInvalidParameters($e->getErrors());
		}
	}
}