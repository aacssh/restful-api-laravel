<?php
use \HairConnect\Transformers\UsersTransformer;

class UsersController extends \BaseController {
    /**
     * @var
     */
    protected $usersTransformer;


    /**
     * @param UsersTransformer $usersTransformer
     */
    public function __construct(UsersTransformer $usersTransformer){
        $this->usersTransformer = $usersTransformer;
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $validator = Validator::make(Input::all(),[
            'name'              =>  'required',
            'username' 			=>  'required|max:20|min:2|unique:users',
            'password' 			=>  'required|min:6',
            'confirm_password' 	=>  'required|same:password',
            'email' 			=>  'required|max:60|email|unique:users'
        ]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}