<?php
namespace HairConnect\Transformers;

/**
 * Class ImagesTransformer
 * @package HairConnect\Transformers
 */
class BarbersAppointmentsTransformer extends Transformers{

	/**
     * This function transformss a data of a user(barber) into json
     * @param  object $appointment
     * @return array       
     */
	public function transform($appointment)
	{
		$keys = array_keys($appointment);
		return [
			$keys[0] => (int)$appointment['appointment_id'],
			$keys[1] => $appointment['client_name'],
			$keys[2] => $appointment['client_username'],
			$keys[3] => $appointment['cancelled'],
			$keys[4] => $appointment['time'],
			$keys[5] => $appointment['date']
		];
	}
}