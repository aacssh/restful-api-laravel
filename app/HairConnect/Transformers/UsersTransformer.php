<?php
/**
 * Created by PhpStorm.
 * User: panchase
 * Date: 9/2/14
 * Time: 10:25 AM
 */

namespace HairConnect\Transformers;


/**
 * Class UsersTransformer
 * @package HairConnect\Transformers
 */
class UsersTransformer extends Transformers{

    /**
     * @param $user
     * @return array
     */
    public function transform($user){
        $mname = ($user['mname'] !== '') ? ' '.$user['mname'] : '';
        return [
            'username'      =>  $user['username'],
            'name'          =>  $user['fname'].$mname.' '.$user['lname'],
            'profile_image' =>  $user['image'],
            'contact_no'    =>  $user['contact_no'],
            'email'         =>  $user['email'],
            'online'        =>  (boolean)$user['active'],
            'activate'      =>  (boolean)$user['deleted'],
            'group'         =>  ($user['group']) ? 'customer' : 'barber',
            'member_since'  =>  $user['created_at']
        ];
    }
} 