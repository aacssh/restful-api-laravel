<?php
use HairConnect\Services\AppointmentService;
use \HairConnect\Transformers\BarbersAppointmentsTransformer;

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

	function __construct(BarbersAppointmentsTransformer $transformer, APIResponse $api, AppointmentService $service){
		parent::__construct($transformer, $api, $service);
	}
}