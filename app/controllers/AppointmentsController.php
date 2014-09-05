<?php
use \HairConnect\Transformers\AppointmentsTransformer;

class AppointmentsController extends \BaseController {

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
			$barber 							= 	Barber::where('login_id', '=', $login_id->first()->id);

			if($barber->count()){
				$barber 						= 	$barber->first();
				$appointments 					= 	Appointment::where('barber_id', '=', $barber->id)->get();
				
				if($appointments->count()){
					$clients 					= 	[];
					foreach ($appointments as $appointment) {
						$client 				= 	Client::where('id', '=', $appointment->client_id)->get()->first();
						$date 					= 	Date::where('id', '=', $appointment->date_id)->get()->first();
						
						array_push($clients,[
							'appointment_id'	=>	$appointment->id,
							'client_name' 		=>	$client->fname.' '.$client->lname,
							'client_id'			=>	$client->id,
							'time' 				=>	$appointment->time,
							'cancelled'			=>	(bool)$appointment->deleted,
							'date'				=>	$date->date
						]);
					}

					return Response::json([
						'appointments' 			=> $clients
					]);
				}
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
			$barber 							= Barber::where('login_id', '=', $login_id->first()->id);

			if($barber->count()){
				$barber 						= $barber->first();
				$appointment 					= Appointment::where('id', '=', $appointmentId)
															->where('barber_id', '=', $barber->id)->get();
				
				if($appointment->count()){
					$appointment 				= $appointment->first();
					$client 					= Client::where('id', '=', $appointment->client_id)->get()->first();
					$date 						= Date::where('id', '=', $appointment->date_id)->get()->first();

					return Response::json([
						'appointment' 	=> [
							'client_name' 		=>	$client->fname.' '.$client->lname,
							'client_username'	=>	$client->id,
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