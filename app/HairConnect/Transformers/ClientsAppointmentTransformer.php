<?php
namespace HairConnect\Transformers;

/**
 * Class ImagesTransformer
 * @package HairConnect\Transformers
 */
class ClientsAppointmentTransformer extends Transformers{

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
			$keys[1] => $appointment['barber_name'],
			$keys[2] => $appointment['barber_username'],
			$keys[3] => (bool)$appointment['cancelled'],
			$keys[4] => $appointment['time'],
			$keys[5] => $appointment['date']
		];
	}
}