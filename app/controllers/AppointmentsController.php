<?php
use \HairConnect\Transformers\AppointmentsTransformer;

abstract class AppointmentsController extends \BaseController {

	/**
	 * [$appointmentsTransformer description]
	 * @var [type]
	 */
	protected $appointmentsTransformer;

	/**
	 * [$apiController description]
	 * @var [type]
	 */
	protected $apiController;


	/**
	 * [$mainClass description]
	 * @var [type]
	 */
	protected $mainClass;

	/**
	 * [$secClass description]
	 * @var [type]
	 */
	protected $secClass;

	/**
	 * [__construct description]
	 * @param AppointmentsTransformer $appointmentsTransformer [description]
	 */
	function __construct(AppointmentsTransformer $appointmentsTransformer, APIController $apiController){
		$this->appointmentsTransformer 	= 	$appointmentsTransformer;
		$this->apiController 			=	$apiController;
	}

	/**
	 * Display a listing of the resource.
	 * 
	 * @param  string $username	 
	 * @return Response
	 */
	public function index($username)
	{	
		$user = $this->getMainUser($username);

		if($user->count()){
			$limit			=	Input::get('limit') ?: 5;
			$appointments	= 	Appointment::where("{$this->mainClass}_id", '=', $user->id)->paginate($limit);
			$total 			=	$appointments->getTotal();
			
			if($appointments->count()){
				$secClass_appointments   = 	[];
				foreach ($appointments as $appointment) {
					$secClass = $this->getSecondaryUser($appointment->client_id);
					$date 	  =	Date::where('id', '=', $appointment->date_id)->get()->first();
					
					array_push($secClass_appointments,[
						'appointment_id'		=>	(int)$appointment->id,
						"{$this->secClass}_name" =>	$secClass->fname.' '.$secClass->lname,
						"{$this->secClass}_id"	=>	(int)$secClass->id,
						'time' 					=>	$appointment->time,
						'cancelled'				=>	(bool)$appointment->deleted,
						'date'					=>	$date->date
					]);
				}

				return $this->apiController->respond([
					'appointments' 	=> $secClass_appointments,
		            'paginator'		=>	[
		            	'total_count'	=>	$total,	
		            	'total_pages'	=>	ceil($total/$appointments->getPerPage()),
		            	'current_page'	=>	$appointments->getCurrentPage(),
		            	'limit'			=>	(int)$limit,
		            	'prev'			=>	$appointments->getLastPage()
		            ]
				]);
			}
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
		$user = $this->getMainUser($username);

		if($user->count()){
			$appointment    =	Appointment::where('id', '=', $appointmentId)
										->where("{$this->mainClass}_id", '=', $user->id)->get();
			
			if($appointment->count()){
				$appointment	=	$appointment->first();
				$secClass 		= $this->getSecondaryUser($appointment->client_id);
				$date 			= 	Date::where('id', '=', $appointment->date_id)->get()->first();

				return $this->apiController->respond([
					'appointment' 	=> [
						"{$this->secClass}_name"		=>	$secClass->fname.' '.$secClass->lname,
						"{$this->secClass}_username"	=>	$secClass->id,
						'canceled'						=>	(bool)$appointment->deleted,
						'time' 							=>	$appointment->time,
						'date'							=>	$date->date
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
		$user = $this->getMainUser($username);

		if($user->count()){
			$appointment    = 	Appointment::where('id', '=', $appointmentId)
									->where("{$this->mainClass}_id", '=', $user->id)->where('deleted', '=', 0)->get();

			if($appointment->count()){
				$appointment 			= $appointment->first();
				$appointment->deleted 	= 1;
				$appointment->save();

				return $this->apiController->respond([
					'message' 	=>	'Appointment has been successfully cancelled.',
					'cancelled'	=>	(bool)$appointment->deleted
				]);
			}
			return $this->apiController->respondNotFound('Appointment not found or already cancelled.');
		}
		return $this->apiController->respondNotFound('user does not exist.');
	}

	abstract public function getMainUser($username);

	abstract public function getSecondaryUser($id);
}