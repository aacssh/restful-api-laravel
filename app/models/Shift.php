<?php

class Shift extends \Eloquent {

	/**
     * This variable specifies which attributes should be guarded against mass-assignable.
     * @var array
     */
    protected $fillable = ['barber_id', 'start_time', 'end_time', 'time_gap', 'deleted'];

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'shifts';
}