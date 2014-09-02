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
     * @param $users
     * @return array
     */
    public function transform($users){
        $mname = ($users['mname'] !== '') ? ' '.$users['mname'] : '';
        return [
            'username'      =>  $users['username'],
            'name'          =>  $users['fname'].$mname.' '.$users['lname'],
            'profile_image' =>  $users['image'],
            'contact_no'    =>  $users['contact_no'],
            'email'         =>  $users['email'],
            'online'        =>  (boolean)$users['active'],
            'activate'      =>  (boolean)$users['deleted'],
            'group'         =>  ($users['group']) ? 'customer' : 'barber',
            'member_since'  =>  $users['created_at']
        ];
    }
} 