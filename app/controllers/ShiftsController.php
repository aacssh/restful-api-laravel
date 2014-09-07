<?php
use \HairConnect\Transformers\ShiftsTransformer;

class ShiftsController extends \BaseController {
	/**
	 * [$shiftsTransformer description]
	 * @var [type]
	 */
	protected $shiftsTransformer;

	/**
	 * [__construct description]
	 * @param [type] $shiftsTransformer [description]
	 */
	function __construct(ShiftsTransformer $shiftsTransformer){
		$this->shiftsTransformer 	=	$shiftsTransformer;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($username)
	{
		$log						=	User::whereUsername($username)->get();
		if($log->count()){
			$barber					=	Barber::where('login_id', '=', $log->first()->id)->get();
			if($barber->count()){
				$barber 			= 	$barber->first();
				$shift 				= 	Shift::where('barber_id', '=', $barber->id)->get();

				if($shift->count()){
					return Response::json([
						'shifts'	=>	$this->shiftsTransformer->transformCollection($shift->all())
					]);
				}
				
			}
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($username)
	{
		$validator = Validator::make(Input::all(),[
            'name'              =>  'required',
            'username' 			=>  'required|max:20|min:2|unique:users',
            'password' 			=>  'required|min:6',
            'confirm_password' 	=>  'required|same:password',
            'email' 			=>  'required|max:60|email|unique:users'
        ]);

        if(!$validator->fails()){
        	$barber = Barber::whereUsername($username)->get();
        	if($barber->count()){
        		$shift 	=	new Shift;
        		$shift->barber_id	=	$barber->first()->id;
        		$shift->start_time 	=	Input::get('start_time');
        		$shift->end_time	=	Input::get('end_time');
        		$shift->time_gap	=	Input::get('time_gap');
        		
        		if($shift->save()){
        			return Response::json([
        				'data'	=>	[
        					'message'		=>	'Shift has been created.',
        					'shift_details'	=>	Shift::
        				]
        			]);
        		}
        	}
        }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function show($username, $shiftId)
	{
		$log						=	User::whereUsername($username)->get();
		if($log->count()){
			$barber					=	Barber::where('login_id', '=', $log->first()->id)->get();
			if($barber->count()){
				$barber 			= 	$barber->first();
				$shift 				= 	Shift::find($shiftId)->where('barber_id', '=', $barber->id)->get();

				if($shift->count()){
					return Response::json([
						'shifts'	=>	$this->shiftsTransformer->transform($shift->first())
					]);
				}
				
			}
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function update($username, $shiftId)
	{
		$log						=	User::whereUsername($username)->get();
		if($log->count()){
			$barber					=	Barber::where('login_id', '=', $log->first()->id)->get();
			if($barber->count()){
				$barber 			= 	$barber->first();
				$shift 				= 	Shift::find($shiftId)->where('barber_id', '=', $barber->id)->get();

				if($shift->count()){
					$shift 			= 	$shift->first();
					$shift->deleted =	1;
					$shift->save();

					return Response::json([
						'success'	=> [
							'message'	=>	'Shift has been updated.',
							'shifts'	=>	$this->shiftsTransformer->transform(
								Shift::find($shiftId)->where('barber_id', '=', $barber->id)->get()->first()
							)
						]
					]);
				}
				
			}
		}
	}
}