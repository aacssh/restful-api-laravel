<?php

class Date extends \Eloquent {
	
	/**
	 *  This variable specifies which attributes should be mass-assignable.
	 * @var array
	 */
    protected $fillable = ['date'];

    /**
     * This variable overrides Laravel convention of creating created_at and 
     * updated_at column in table and tells it not to create them.
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'dates';
}