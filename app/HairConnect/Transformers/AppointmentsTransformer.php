<?php
namespace HairConnect\Transformers;

/**
 * Class ImagesTransformer
 * @package HairConnect\Transformers
 */
class AppointmentsTransformer extends Transformers{

	/**
     * This function transformss a data of a user(barber) into json
     * @param  object $appointment
     * @return array       
     */
	public function transform($appointment)
	{
		return [
			'id' => (int)$appointment->appointment_id,
			'barber' => $appointment->barber_name,
			'barber_id' => (int)$appointment->barber_id,
			'time' => $appointment->time,
			'active' => $appointment->cancelled,
			'date' => $appointment->date
		];
	}
}