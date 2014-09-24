<?php

class HairStyleImage extends \Eloquent {

	/**
     * This variable specifies which attributes should be mass-assignable.
     * @var array
     */
    protected $fillable = ['user_id', 'image', 'image_title'];

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $tables = 'hair_style_images';
}