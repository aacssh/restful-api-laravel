<?php

namespace HairConnect\Transformers;

class ClientsTransformer extends Transformers{

	/**
	 * [transform description]
	 * @param  [type] $client [description]
	 * @return [type]         [description]
	 */
	public function transform($client)
	{
		$mname = ($client['mname'] !== '') ? ' '.$client['mname'] : '';
        return [
            'username'      =>  $client['username'],
            'name'          =>  $client['fname'].$mname.' '.$client['lname'],
            'profile_image' =>  $client['image'],
            'contact_no'    =>  $client['contact_no'],
            'email'         =>  $client['email'],
            'online'        =>  (boolean)$client['active'],
            'activate'      =>  (boolean)$client['deleted'],
            'group'         =>  ($client['group']) ? 'customer' : 'barber',
            'member_since'  =>  $client['created_at']
        ];
	}
}