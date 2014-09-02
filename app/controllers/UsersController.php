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
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $users = User::all();
        return Response::json([
           'data' => $this->usersTransformer->transformCollection($users->all())
        ]);
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
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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