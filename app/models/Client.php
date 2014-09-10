<?php

class Client extends \Eloquent {
    protected $fillable = [
    	'fname', 'mname', 'lname', 'image', 'contact_no', 'active', 'deleted'
    ];

    protected $table	=	'clients';

    public function user()
    {
    	return $this->belongsTo('User');
    }
}