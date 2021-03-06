<?php

class Appointment extends \Eloquent {

	/**
   * This variable specifies which attributes should be guarded against mass-assignable.
   * @var array
   */
  protected $fillable = ['barber_id', 'client_id', 'time', 'date_id', 'deleted' ];

  /**
   * Establishing a relationship between Appointment and User model.
   * This function specifies that Appointment belongs to a User model.
   * @return mixed
   */
  public function user(){
		return $this->belongsTo('User');
	}

  public function findById($id){
    if(!is_null($appointment = static::find($id))){
      return $appointment;
    }
    throw new NotFoundException('Given appointment id do not match with any appointment.');
  }

  public function findByUserWithPaginate($user, $id, $limit){
    if(!is_null($appointment = static::where($user, '=', $id)->paginate($limit))){
      return $appointment;
    }
    throw new NotFoundException('Given appointment id do not match with any appointment.');
  }
}