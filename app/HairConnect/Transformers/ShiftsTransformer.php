<?php
namespace HairConnect\Transformers;

/**
 * Class ShiftsTransformer
 * @package HairConnect\Transformers
 */
class ShiftsTransformer extends Transformers{

	/**
	 * This function transformss a data of a shift into json
	 * @param  object $shift 
	 * @return array       
	 */
	public function transform($shift)
	{
		return [
			'id'			=>	(int)$shift->id,
			'starting_hour'	=>	$shift->start_time,
			'ending_hour'	=>	$shift->end_time,
			'shift_gap'		=>	(int)$shift->time_gap,
			'date'			=>	(int)$shift->date_id,
			'deleted'		=>	(bool)$shift->deleted
		];
	}
}