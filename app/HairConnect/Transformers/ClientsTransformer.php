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
		$login_details  =   \User::find($client['login_id']);
        
        return [
            'username'      =>  $login_details->username,
            'name'          =>  $client['fname'].' '.$client['lname'],
            'profile_image' =>  $client['image'],
            'contact_no'    =>  $client['contact_no'],
            'email'         =>  $login_details->email,
            'online'        =>  (boolean)$client['active'],
            'activate'      =>  (boolean)$client['deleted'],
            'member_since'  =>  $client['created_at'],
            'resource_uri'	=>  \URL::to('/').'/clients/'.$login_details->username
        ];
	}
}