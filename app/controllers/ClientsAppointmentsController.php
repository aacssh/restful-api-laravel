<?php
use \HairConnect\Transformers\AppointmentsTransformer;
use \HairConnect\Services\AppointmentService;
use \HairConnect\Validators\ValidationException;

class ClientsAppointmentsController extends \BaseController{	
	/**
	 * [$appointmentsTransformer description]
	 * @var object
	 */
	protected $appointmentsTransformer;

	/**
	 * [$apiController description]
	 * @var object
	 */
	protected $apiController;

	/**
	 * [$appointmentService description]
	 * @var object
	 */
	protected $appointmentService;

	/**
	 * [__construct description]
	 * @param AppointmentsTransformer $appointmentsTransformer [description]
	 */
	function __construct(AppointmentsTransformer $appointmentsTransformer, APIController $apiController, AppointmentService $appointmentService){
		$this->appointmentsTransformer 	= 	$appointmentsTransformer;
		$this->apiController 			=	$apiController;
		$this->appointmentService 		=	$appointmentService;
	}

	/**
	 * Display a listing of the resource.
	 * 
	 * @param  string $username	 
	 * @return Response
	 */
	public function index($username)
	{
		$client = 	User::findByUsernameOrFail($username)->client;

		if($client->count()){
			$limit			=	Input::get('limit') ?: 5;
			$appointments 	= 	Appointment::where('client_id', '=', $client->id)->paginate($limit);
			$total 			=	$appointments->getTotal();
			
			if($appointments->count()){
				$client_appointments	=	[];
				foreach ($appointments as $appointment) {
					$barber = 	Barber::where('id', '=', $appointment->barber_id)->get()->first();
					$date 	= 	Date::where('id', '=', $appointment->date_id)->get()->first();
					
					array_push($client_appointments,[
						'appointment_id'	=>	$appointment->id,
						'barber_name' 		=>	$barber->fname.' '.$barber->lname,
						'barber_id'			=>	$barber->id,
						'time' 				=>	$appointment->time,
						'cancelled'			=>	(bool)$appointment->deleted,
						'date'				=>	$date->date
					]);
				}

				return $this->apiController->respond([
					'appointments'	=> $this->appointmentsTransformer->transformCollection($client_appointments),
		            'paginator'		=>	[
		            	'total_count'	=>	$total,	
		            	'total_pages'	=>	ceil($total/$appointments->getPerPage()),
		            	'current_page'	=>	$appointments->getCurrentPage(),
		            	'limit'			=>	(int)$limit,
		            	'prev'			=>	$appointments->getLastPage()
		            ]
				]);
			}
			return $this->apiController->respond([
				'message' 	=>	$username, ' have no appointments.'
			]);
		}
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

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $username
	 * @param  integer $appointmentId
	 * @return Response
	 */
	public function show($username, $appointmentId)
	{
		$client = 	User::findByUsernameOrFail($username)->client;

		if($client->count()){
			$appointment    = 	Appointment::where('id', '=', $appointmentId)
										->where('client_id', '=', $client->id)->get();
			
			if($appointment->count()){
				$appointment	= 	$appointment->first();
				$barber 		= 	Barber::where('id', '=', $appointment->barber_id)->get()->first();
				$date 			= 	Date::where('id', '=', $appointment->date_id)->get()->first();

				return $this->apiController->respond([
					'appointment' 	=> [
						'barber_name' 		=>	$barber->fname.' '.$barber->lname,
						'barber_id'			=>	$barber->id,
						'canceled'			=>	(bool)$appointment->deleted,
						'time' 				=>	$appointment->time,
						'date'				=>	$date->date
					]
				]);
			}
			return $this->apiController->respondNotFound('Appointment is not found or cancelled or expired.');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function destroy($username, $appointmentId)
	{
		$barber = 	User::findByUsernameOrFail($username)->barber;

		if($barber->count()){
			$barber    	    =	$barber->first();
			$Appointment    =	Appointment::where('id', '=', $appointmentId)
										->where('barber_id', '=', $barber->id)->where('deleted', '=', 0)->get();

			if($appointment->count()){
				$appointment 			= $appointment->first();
				$appointment->deleted 	= 1;
				$appointment->save();

				return $this->apiController->respond([
					'message'	   =>	'Appointment has been successfully cancelled.',
					'cancelled'    =>	(bool)$appointment->deleted
				]);
			}
			return $this->apiController->respondNotFound('Appointment not found or already cancelled.');
		}
	
		return $this->apiController->respondNotFound('Barber does not exist.');
	}
}