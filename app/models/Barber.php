<?php

class Barber extends \Eloquent {
    protected $fillable = [
    	'fname', 'mname', 'lname', 'image', 'contact_no', 'active', 'deleted'
    ];

    protected $table	=	'barbers';

}