<?php

class Client extends \Eloquent {
    protected $fillable = [
    	'fname', 'mname', 'lname', 'image', 'contact_no', 'active', 'deleted'
    ];

    protected $table	=	'clients';
}