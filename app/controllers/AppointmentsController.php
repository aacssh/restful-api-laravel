<?php
use \HairConnect\Transformers\AppointmentsTransformer;

abstract class AppointmentsController extends TokensController {

	/**
	 * Stores object of AppointmentsTransformer
	 * @var ApointmentsTransformer
	 */
	protected $transformer;

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
	function __construct(AppointmentsTransformer $transformer, APIResponse $api){
		$this->transformer = $transformer;
		$this->api = $api;
	}

	/**
	 * Get the secondary type of user
	 * @param  int $id
	 * @return object
	 */
	public function getSecondaryUserType($id){
		return User::find($id);
	}

	/**
	 * Display a listing of all appointments according to search parameter
	 * 
	 * @param  string $username	 
	 * @return Response
	 */
	public function index($username)
	{
		if(($user = $this->checkTokenAndUsernameExists(Input::get('token'), $username)) != false){
			$limit = Input::get('limit') ?: 5;
			$appointments =	Appointment::where("{$this->mainUserType}_id", '=', $user->id)->paginate($limit);
			$totalAppointments = $appointments->getTotal();
			
			if($appointments->count()){
				$appointmentsOfSecUserType = [];
				foreach ($appointments as $appointment){
					if($this->secUserType == 'barber'){
						$secUserType = $this->getSecondaryUserType($appointment->barber_id);
					}else if($this->secUserType == 'client'){
						$secUserType = $this->getSecondaryUserType($appointment->client_id);
					}

					$date =	Date::where('id', '=', $appointment->date_id)->get()->first();
					array_push($appointmentsOfSecUserType,[
						'appointment_id' 			=>	(int)$appointment->id,
						"{$this->secUserType}_name" =>	$secUserType->fname.' '.$secUserType->lname,
						"{$this->secUserType}_id"	=>	(int)$secUserType->id,
						'time' 						=>	$appointment->time,
						'cancelled'					=>	(bool)$appointment->deleted,
						'date'						=>	$date->date
					]);
				}
				
				return $this->api->respond([
					'appointments' 	=> $appointmentsOfSecUserType,
          'paginator'		=>	[
          	'total_count'	=>	$totalAppointments,	
          	'total_pages'	=>	ceil($totalAppointments/$appointments->getPerPage()),
          	'current_page'	=>	$appointments->getCurrentPage(),
          	'limit'			=>	(int)$limit,
          	'prev'			=>	$appointments->getLastPage()
          ]
				]);
			}
			return $this->api->respondNoContent('You have no appointment.');
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * Display a listing of the appointment of specified appointment id
	 *
	 * @param  string  $username
	 * @param  integer $appointmentId
	 * @return Response
	 */
	public function show($username, $appointmentId)
	{
		if($this->checkTokenAndUsernameExists(Input::get('token'), $username) != false){
			$appointment = Appointment::find($appointmentId);
			if($appointment->count()){
				if($this->secUserType == 'barber'){
					$secUserType = $this->getSecondaryUserType($appointment->barber_id);
				}else if($this->secUserType == 'client'){
					$secUserType = $this->getSecondaryUserType($appointment->client_id);
				}
				$date =	Date::where('id', '=', $appointment->date_id)->get()->first();

				return $this->api->respond([
					'appointment' => [
						//"saloon_name"				=>	$secUserType->shop_name,
						"{$this->secUserType}_name"		=>	$secUserType->fname.' '.$secUserType->lname,
						"{$this->secUserType}_username"	=>	$secUserType->username,
						'canceled'					=>	(bool)$appointment->deleted,
						'time' 						=>	$appointment->time,
						'date'						=>	$date->date
					]
				]);
			}
			return $this->api->respondNotFound('Appointment does not exist.');
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * Remove appointment of the specified appointment id
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function destroy($username, $appointmentId)
	{
		if($this->checkTokenAndUsernameExists(Input::get('token'), $username) != false){
			$appointment = Appointment::find($appointmentId);

			if($appointment->count()){
				$appointment->deleted = 1;
				$appointment->save();
				return $this->api->respondSuccess('Appointment has been successfully cancelled.',);
			}
			return $this->api->respondNotFound('Appointment not found or already cancelled.');
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}
}