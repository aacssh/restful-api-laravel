<?php
namespace HairConnect\Transformers;

/**
 * @package HairConnect\Transformers
 */
class UsersTransformer extends Transformers{

	/**
     * This function transformss a data of a user(user) into json
     * @param  object $user
     * @return array       
     */
	public function transform($user)
	{
        return [
        	'group'			=>	$user->type,
            'username'      =>  $user->username,
            'name'          =>  $user->fname.' '.$user->lname,
            'access_token'	=>	$user->access_token,
            'profile_image' =>  ($user->image ? $user->image : ''),
            'contact_no'    =>  ($user->contact_no ? ($user->contact_no + 0) : ''),
            'email'         =>  $user->email,
            'address'       =>  ($user->address ? $user->address : ''),
            'zip'           =>  ($user->zip ? ($user->zip + 0) : ''),
            'online'        =>  (boolean)$user->online,
            'deactivated'   =>  (boolean)$user->deactivated,
            'member_since'  =>  $user->created_at
        ];
	}
}