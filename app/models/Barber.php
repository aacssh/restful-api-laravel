<?php

class Barber extends \Eloquent {
    protected $fillable = [
    	'fname', 'mname', 'lname', 'image', 'contact_no', 'active', 'deleted'
    ];

    protected $table	=	'barbers';

    public function user()
    {
    	return $this->belongsTo('User');
    }

    /*
    public function shifts()
    {
    	return $this->hasMany('Shift', 'barber_id');
    }
	*/
}