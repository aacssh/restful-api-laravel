<?php
namespace HairConnect\Transformers;

/**
 * Class ImagesTransformer
 * @package HairConnect\Transformers
 */
class ImagesTransformer extends Transformers{
	
	/**
	 * This function transformss a data of a image into json
	 * @param  object $image
	 * @return array       
	 */
	public function transform($image)
	{
		return [
			'hair_style' => $image->image,
			'hair_style_title' => $image->image_title
		];
	}
}