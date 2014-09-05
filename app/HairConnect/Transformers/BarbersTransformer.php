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
        $login_details  =   \User::find($barber['login_id']);

        return [
            'username'      =>  $login_details->username,
            'name'          =>  $barber['fname'].' '.$barber['lname'],
            'profile_image' =>  $barber['image'],
            'contact_no'    =>  $barber['contact_no'],
            'email'         =>  $login_details->email,
            'online'        =>  (boolean)$barber['active'],
            'activate'      =>  (boolean)$barber['deleted'],
            'member_since'  =>  $barber['created_at'],
            'resource_uri'	=>  \URL::to('/').'/barbers/'.$login_details->username
        ];
    }
} 