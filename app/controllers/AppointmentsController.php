<?php
use \HairConnect\Transformers\AppointmentsTransformer;

class AppointmentsController extends \BaseController {

	/**
	 * [$appointmentsTransformer description]
	 * @var [type]
	 */
	protected $appointmentsTransformer;


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
		$user = User::whereUsername($username)->get();
		if($user->count()){
			$user = $user->first();
			$appointments = Appointment::where('barber_id', '=', $user->id)->get();
			
			if($appointments->count()){
				$clients = [];
				foreach ($appointments as $appointment) {
					$user = User::where('id', '=', $appointment->client_id)->get()->first();
					$date = Date::where('id', '=', $appointment->date_id)->get()->first();
					
					array_push($clients,[
						'appointment_id'	=>	$appointment->id,
						'client_name' 		=>	$user->fname.' '.$user->lname,
						'client_username'	=>	$user->username,
						'time' 				=>	$appointment->time,
						'cancelled'			=>	(bool)$appointment->deleted,
						'date'				=>	$date->date
					]);
				}

				return Response::json([
					'appointments' 	=> $clients
				]);
			}
		}
		/*
		if($user->count()){
			return Response::json([
				'appointments' => $this->appointmentsTransformer->transformCollection(Appointment::where('barber_id', '=', $user->first()->id)->get())
			]);
		}
		*/
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
		$user = User::whereUsername($username)->get();
		if($user->count()){
			$user = $user->first();
			$appointment = Appointment::where('id', '=', $appointmentId)->where('barber_id', '=', $user->id)->get();

			
			if($appointment->count()){
				$appointment = $appointment->first();
				$user = User::where('id', '=', $appointment->client_id)->get()->first();
				$date = Date::where('id', '=', $appointment->date_id)->get()->first();

				return Response::json([
					'appointment' 	=> [
						'client_name' 		=>	$user->fname.' '.$user->lname,
						'client_username'	=>	$user->username,
						'canceled'			=>	(bool)$appointment->deleted,
						'time' 				=>	$appointment->time,
						'date'				=>	$date->date
					]
				]);
			}
			return Response::json([
				'error' => [
					'message'	=>	'Appointment is not found or cancelled or expired.'
				]
			]);
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
		$user = User::whereUsername($username)->get();
		if($user->count()){
			$user = $user->first();
			$appointment = Appointment::where('id', '=', $appointmentId)
							->where('barber_id', '=', $user->id)
							->where('deleted', '=', 0)->get();

			if($appointment->count()){
				$appointment = $appointment->first();
				$appointment->deleted = 1;
				$appointment->save();

				return Response::json([
					'message' 	=> 'Appointment has been successfully cancelled.',
					'cancelled'	=>	(bool)$appointment->deleted
				]);
			}
		}
		return Response::json([
			'error' => [
				'message'	=>	'User or appointment not found.'
			]
		]);
	}

}