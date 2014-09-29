<?php
use \HairConnect\Transformers\Transformers;
use HairConnect\Services\AppointmentService;

abstract class AppointmentsController extends TokensController {

	/**
	 * Stores object of AppointmentsTransformer
	 * @var ApointmentsTransformer
	 */
	protected $transformer;
	protected $service;

	/**
	 * Stores object of APIController
	 * @var APIController
	 */
	protected $api;

	/**
	 * Prepare the object of the controller for use
	 * @param AppointmentsTransformer $transformer
	 * @param APIController           $api 
	 */
	function __construct(Transformers $transformer, APIResponse $api, AppointmentService $service){
		$this->transformer = $transformer;
		$this->api = $api;
		$this->service = $service;
		$this->service->setUsers($this->mainUserType, $this->secUserType);
	}

	/**
	 * Display a listing of all appointments according to search parameter
	 * 
	 * @param  string $username	 
	 * @return Response
	 */
	public function index($username){
		try{
			$this->service->showAll(Input::all(), $username);
			return $this->api->respondSuccessWithDetails(
				'Appointment successfully retrieved.', [
					'appoiontments' => $this->transformer->transformCollection($this->service->getAppointmentDetails()), 
					'paginator' => $this->service->getPaginator()
				]);
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
	}

	/**
	 * Display a listing of the appointment of specified appointment id
	 *
	 * @param  string  $username
	 * @param  integer $appointmentId
	 * @return Response
	 */
	public function show($username, $appointmentId){
		try{
			$this->service->show(Input::all(), $username, $appointmentId);
			return $this->api->respondSuccessWithDetails('Appointment successfully retrieved.', $this->transformer->transform($this->service->getAppointmentDetails()));
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
	}

	/**
	 * Remove appointment of the specified appointment id
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function destroy($username, $appointmentId){
		try{
			$this->service->destroy(Input::all(), $username, $appointmentId);	
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());	
		}
	}

	/**
	 * Get the secondary type of user
	 * @param  int $id
	 * @return object
	 */
	public function getSecondaryUserType($id){
		return User::find($id);
	}
}