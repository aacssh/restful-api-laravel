<?php
use \HairConnect\Transformers\AppointmentsTransformer;

class ClientsAppointmentsController extends \BaseController{	
	/**
	 * [$appointmentsTransformer description]
	 * @var [type]
	 */
	protected $appointmentsTransformer;

	/**
	 * [__construct description]
	 * @param AppointmentsTransformer $appointmentsTransformer [description]
	 */
	function __construct(AppointmentsTransformer $appointmentsTransformer){
		$this->appointmentsTransformer = $appointmentsTransformer;
	}

	/**
	 * Display a listing of the resource.
	 * 
	 * @param  string $username	 
	 * @return Response
	 */
	public function index($username)
	{
		$login_id								=	User::whereUsername($username)->get();
		
		if($login_id->count()){
			$client 							= 	Client::where('login_id', '=', $login_id->first()->id);

			if($client->count()){
				$client 						= 	$client->first();
				$appointments 					= 	Appointment::where('client_id', '=', $client->id)->get();
				
				if($appointments->count()){
					$client_appointments 				= 	[];
					foreach ($appointments as $appointment) {
						$barber 				= 	Barber::where('id', '=', $appointment->barber_id)->get()->first();
						$date 					= 	Date::where('id', '=', $appointment->date_id)->get()->first();
						
						array_push($client_appointments,[
							'appointment_id'	=>	$appointment->id,
							'barber_name' 		=>	$barber->fname.' '.$barber->lname,
							'barber_id'			=>	$barber->id,
							'time' 				=>	$appointment->time,
							'cancelled'			=>	(bool)$appointment->deleted,
							'date'				=>	$date->date
						]);
					}
					return Response::json([
						'appointments' 			=> $client_appointments
					]);
				}
				return Response::json([
					'message' 					=> $username, ' have no appointments.'
				]);
			}
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
		$login_id								=	User::whereUsername($username)->get();
		
		if($login_id->count()){
			$client 							= 	Client::where('login_id', '=', $login_id->first()->id);

			if($client->count()){
				$client 						= 	$client->first();
				$appointment 					= 	Appointment::where('id', '=', $appointmentId)
																->where('client_id', '=', $client->id)->get();
				
				if($appointment->count()){
					$appointment 				= 	$appointment->first();
					$barber 					= 	Barber::where('id', '=', $appointment->barber_id)->get()->first();
					$date 						= 	Date::where('id', '=', $appointment->date_id)->get()->first();

					return Response::json([
						'appointment' 	=> [
							'barber_name' 		=>	$barber->fname.' '.$barber->lname,
							'barber_id'			=>	$barber->id,
							'canceled'			=>	(bool)$appointment->deleted,
							'time' 				=>	$appointment->time,
							'date'				=>	$date->date
						]
					]);
				}
				return Response::json([
					'error' => [
						'message'				=>	'Appointment is not found or cancelled or expired.'
					]
				]);
			}
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
		$login_id							=	User::whereUsername($username)->get();
		
		if($login_id->count()){
			$barber 						= Barber::where('login_id', '=', $login_id->first()->id);

			if($barber->count()){
				$barber 					= $barber->first();
				$appointment 				= Appointment::where('id', '=', $appointmentId)
														->where('barber_id', '=', $barber->id)
														->where('deleted', '=', 0)->get();

				if($appointment->count()){
					$appointment 			= $appointment->first();
					$appointment->deleted 	= 1;
					$appointment->save();

					return Response::json([
						'message' 			=> 'Appointment has been successfully cancelled.',
						'cancelled'			=>	(bool)$appointment->deleted
					]);
				}
				return Response::json([
					'error' => [
						'message'			=>	'Appointment not found or already cancelled.'
					]
				]);
			}
		}
		return Response::json([
			'error' => [
				'message'					=>	'Barber does not exist.'
			]
		]);
	}
}