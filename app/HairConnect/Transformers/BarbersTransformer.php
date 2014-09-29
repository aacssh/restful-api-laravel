<?php
namespace HairConnect\Transformers;

/**
 * Class BarbersTransformer
 * @package HairConnect\Transformers
 */
class BarbersTransformer extends Transformers{

  protected $imagesTransformer;

  function __construct(ImagesTransformer $imagesTransformer){
    $this->imagesTransformer = $imagesTransformer;
  }

 	/**
   * This function transformss a data of a user(barber) into json
   * @param  object $barber
   * @return array       
   */
  public function transform($barber){
    return [
      'saloon_name'  =>	$barber['shop_name'],
      'username'  =>  $barber['username'],
      'name'  =>  $barber['fname'].' '.$barber['lname'],
      'profile_image'  =>  $barber['image'],
      'contact_no'  =>  $barber['contact_no'] + 0,
      'email'  =>  $barber['email'],
      'address'  =>  $barber['address'],
      'zip'  =>  $barber['zip'] + 0,
      'online'  =>  (boolean)$barber['online'],
      'deactivated'  =>  (boolean)$barber['deactivated'],
      'member_since'  =>  $barber['created_at']
    ];
  }

  public function transformWithImages($barber){
    return [
      'saloon_name'  => $barber->shop_name,
      'username'  =>  $barber->username,
      'name'  =>  $barber->fname.' '.$barber->lname,
      'profile_image'  =>  $barber->image,
      'contact_no'  =>  $barber->contact_no + 0,
      'email'  =>  $barber->email,
      'address'  =>  $barber->address,
      'zip'  =>  $barber->zip + 0,
      'online'  =>  (boolean)$barber->online,
      'deactivated'  =>  (boolean)$barber->deactivated,
      'member_since'  =>  $barber->created_at,
      'hair_style_images' => $this->imagesTransformer->transformCollection($barber->hair_style_images)
    ];
  }
}