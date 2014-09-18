<?php
/**
 * Created by PhpStorm.
 * User: panchase
 * Date: 9/2/14
 * Time: 10:25 AM
 */

namespace HairConnect\Transformers;

/**
 * Class BarbersTransformer
 * @package HairConnect\Transformers
 */
class BarbersTransformer extends Transformers{

    /**
     * @param $barber
     * @return array
     */
    public function transform($barber){
        return [
            'Saloon_name'   =>  $barber['shop_name'],
            'username'      =>  $barber->username,
            'name'          =>  $barber['fname'].' '.$barber['lname'],
            'profile_image' =>  $barber['image'],
            'contact_no'    =>  $barber['contact_no'] + 0,
            'email'         =>  $barber->email,
            'address'       =>  $barber['address'],
            'zip'           =>  $barber['zip'] + 0,
            'online'        =>  (boolean)$barber['online'],
            'deactivated'   =>  (boolean)$barber['deactivated'],
            'member_since'  =>  $barber['created_at'],
            'resource_uri'	=>  \URL::to('/').'/barbers/'.$barber->username
        ];
    }
}