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
        $mname = ($barber['mname'] !== '') ? ' '.$barber['mname'] : '';
        return [
            'username'      =>  $barber['username'],
            'name'          =>  $barber['fname'].$mname.' '.$barber['lname'],
            'profile_image' =>  $barber['image'],
            'contact_no'    =>  $barber['contact_no'],
            'email'         =>  $barber['email'],
            'online'        =>  (boolean)$barber['active'],
            'activate'      =>  (boolean)$barber['deleted'],
            'group'         =>  ($barber['group']) ? 'customer' : 'barber',
            'member_since'  =>  $barber['created_at']
        ];
    }
} 