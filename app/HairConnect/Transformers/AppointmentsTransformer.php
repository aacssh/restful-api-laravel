<?php
namespace HairConnect\Transformers;

class AppointmentsTransformer extends Transformers{
	public function transform($appointment)
	{
		return [
			'id' 		=> (int)$appointment['appointment_id'],
			'barber' 	=> $appointment['barber_name'],
			'barber_id' => (int)$appointment['barber_id'],
			'time'		=>	$appointment['time'],
			'active' 	=> $appointment['cancelled'],
			'date'		=> $appointment['date']
		];
	}
}