<?php
use \HairConnect\Transformers\AppointmentsTransformer;

abstract class AppointmentsController extends TokensController {

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
	 * [__construct description]
	 * @param AppointmentsTransformer $appointmentsTransformer [description]
	 */
	function __construct(AppointmentsTransformer $appointmentsTransformer, APIController $apiController){
		$this->appointmentsTransformer 	= 	$appointmentsTransformer;
		$this->apiController 			=	$apiController;
	}

	/**
	 * [getSecondaryUser description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getSecondaryUser($id){
		return User::find($id);
	}

	/**
	 * Display a listing of the resource.
	 * 
	 * @param  string $username	 
	 * @return Response
	 */
	public function index($username)
	{	
		if(($user = $this->checkToken(Input::get('token'), $username)) != false){
			$limit			=	Input::get('limit') ?: 5;
			$appointments	= 	Appointment::where("{$this->mainType}_id", '=', $user->id)->paginate($limit);
			$total 			=	$appointments->getTotal();
			
			if($appointments->count()){
				$secType_appointments   = 	[];
				foreach ($appointments as $appointment)
				{
					if($this->secType == 'barber'){
						$secType = $this->getSecondaryUser($appointment->barber_id);
					}else if($this->secType == 'client'){
						$secType = $this->getSecondaryUser($appointment->client_id);
					}
					
					$date 	  =	Date::where('id', '=', $appointment->date_id)->get()->first();
					
					array_push($secType_appointments,[
						'appointment_id'		=>	(int)$appointment->id,
						"{$this->secType}_name" =>	$secType->fname.' '.$secType->lname,
						"{$this->secType}_id"	=>	(int)$secType->id,
						'time' 					=>	$appointment->time,
						'cancelled'				=>	(bool)$appointment->deleted,
						'date'					=>	$date->date
					]);
				}
				
				return $this->apiController->respond([
					'appointments' 	=> $secType_appointments,
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
		return $this->apiController->respond([
			'errors' => [
            	'message' => 'Invalid token or User cannot be found.'
            ]
        ]);	
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
		if($this->checkToken(Input::get('token'), $username) != false){
			$appointment    =	Appointment::find($appointmentId);
			if($appointment->count()){
				if($this->secType == 'barber'){
					$secType = $this->getSecondaryUser($appointment->barber_id);
				}else if($this->secType == 'client'){
					$secType = $this->getSecondaryUser($appointment->client_id);
				}
				$date 	= 	Date::where('id', '=', $appointment->date_id)->get()->first();

				return $this->apiController->respond([
					'appointment' 	=> [
						//"saloon_name"				=>	$secType->shop_name,
						"{$this->secType}_name"		=>	$secType->fname.' '.$secType->lname,
						"{$this->secType}_username"	=>	$secType->username,
						'canceled'					=>	(bool)$appointment->deleted,
						'time' 						=>	$appointment->time,
						'date'						=>	$date->date
					]
				]);
			}
			return $this->apiController->respondNotFound('Appointment is cancelled or expired.');
		}
		return $this->apiController->respond([
			'errors' => [
            	'message' => 'Invalid token or User cannot be found.'
            ]
        ]);	
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function destroy($username, $appointmentId)
	{
		if($this->checkToken(Input::get('token'), $username) != false){
			$appointment    = 	Appointment::find($appointmentId);

			if($appointment->count()){
				$appointment->deleted 	= 1;
				$appointment->save();

				return $this->apiController->respond([
					'message' 	=>	'Appointment has been successfully cancelled.',
					'cancelled'	=>	(bool)$appointment->deleted
				]);
			}
			return $this->apiController->respondNotFound('Appointment not found or already cancelled.');
		}
		return $this->apiController->respond([
			'errors' => [
            	'message' => 'Invalid token or User cannot be found.'
            ]
        ]);
	}
}