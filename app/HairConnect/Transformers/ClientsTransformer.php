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
        return [
            'username'      =>  $client->username,
            'name'          =>  $client['fname'].' '.$client['lname'],
            'profile_image' =>  $client['image'],
            'contact_no'    =>  $client['contact_no'] + 0,
            'email'         =>  $client->email,
            'address'       =>  $client['address'],
            'zip'           =>  $client['zip'] + 0,
            'online'        =>  (boolean)$client['online'],
            'deactivated'   =>  (boolean)$client['deactivated'],
            'member_since'  =>  $client['created_at'],
            'resource_uri'  =>  \URL::to('/').'/clients/'.$client->username
        ];
	}
}