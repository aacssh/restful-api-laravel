<?php
namespace HairConnect\Transformers;

class ShiftsTransformer extends Transformers{
	public function transform($shift)
	{
		return [
			'starting_hour'	=>	$shift['start_time'],
			'ending_hour'	=>	$shift['end_time'],
			'shift_gap'		=>	(int)$shift['time_gap'],
			'deleted'		=>	(bool)$shift['deleted']
		];
	}
}