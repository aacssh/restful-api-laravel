<?php
namespace HairConnect\Transformers;

class ShiftsTransformer extends Transformers{
	public function transform($shift)
	{
		return [
			'id'			=>	(int)$shift['id'],
			'starting_hour'	=>	$shift['start_time'],
			'ending_hour'	=>	$shift['end_time'],
			'shift_gap'		=>	(int)$shift['time_gap'],
			'date'			=>	(int)$shift['date_id'],
			'deleted'		=>	(bool)$shift['deleted']
		];
	}
}