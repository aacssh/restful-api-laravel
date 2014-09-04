<?php

class Appointment extends \Eloquent {
    protected $fillable = [];

    /**
     * [user description]
     * @return [type] [description]
     */
    public function user(){
		return $this->belongsTo('User');
	}
}