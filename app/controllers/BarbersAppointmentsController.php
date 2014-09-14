<?php

class BarbersAppointmentsController extends AppointmentsController {

	/**
	 * [$mainClass description]
	 * @var [type]
	 */
	protected $mainClass = 'barber';

	/**
	 * [$secClass description]
	 * @var [type]
	 */
	protected $secClass = 'client';

	public function getMainUser($username){
		return User::findByUsernameOrFail($username)->barber;
	}

	public function getSecondaryUser($id){
		return Client::where('id', '=', $id)->get()->first();
	}
}