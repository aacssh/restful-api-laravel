<?php

class Shift extends \Eloquent {
    protected $fillable 	= 	['barber_id', 'start_time', 'end_time', 'time_gap', 'deleted'];
    protected $table 		=	'shifts';

    /*
    public function barber()
    {
    	return $this->belongsTo('Barber', 'barber_id');
    }
    */
}