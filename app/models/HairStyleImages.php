<?php

class HairStyleImages extends \Eloquent {
    protected $fillable = ['barber_id', 'image', 'image_title'];

    protected $tables = 'hair_style_images';
}