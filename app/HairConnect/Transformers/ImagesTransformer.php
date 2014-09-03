<?php
namespace HairConnect\Transformers;

class ImagesTransformer extends Transformers{
	
	public function transform($image)
	{
		return [
			'hair_style' => $image['image'],
			'hair_style_title' => $image['image_title']
		];
	}
}