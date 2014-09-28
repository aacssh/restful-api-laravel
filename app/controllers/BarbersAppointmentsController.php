<?php
use HairConnect\Services\AppointmentService;

class BarbersAppointmentsController extends AppointmentsController {
	/**
	 * Primary user type of the controller
	 * @var string
	 */
	protected $mainUserType = 'barber';

	/**
	 * Secondary user type of the controller
	 * @var string
	 */
	protected $secUserType = 'client';
}