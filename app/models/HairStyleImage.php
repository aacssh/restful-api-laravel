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

    public function findAllByBarberId($id){
        if(!is_null($images = static::where('user_id', '=', $id)->get())){
            return $images;
        }
        throw new NotFoundException('No hair style image found.');
    }
}