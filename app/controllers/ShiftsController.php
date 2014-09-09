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
            'start_time'    =>  'required',
            'end_time' 		=>  'required',
            'time_gap' 		=>  'required',
            'date' 			=>  'required'
        ]);

        if(!$validator->fails()){
        	$user = User::whereUsername($username)->get();
        	if($user->count()){
        		$barber 	=	Barber::where('login_id', '=', $user->first()->id)->get();
        		$date		=	Date::where('date', '=', Input::get('date'))->get();

        		$shift 	=	new Shift;
        		$shift->barber_id	=	$barber->first()->id;
        		$shift->start_time 	=	Input::get('start_time').':00:00';
        		$shift->end_time	=	Input::get('end_time').':00:00';
        		$shift->time_gap	=	(int)Input::get('time_gap');
        		$shift->date_id 	=	$date->first()->id;
        		
        		if($shift->save()){
        			return Response::json([
        				'data'	=>	[
        					'message'		=>	'Shift has been successfully created.'
        				]
        			]);
        		}
        	}
        	return Response::json([
				'errors' => [
					'message'				=>	'Barber does not exist or Shift cannot be saved.'
				]
			]);
        }
        return Response::json([
			'errors' => [
				'message'				=>	'Shift cannot be stored.',
				'errors_details'		=>	$validator->messages()
			]
		]);
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
				$shift 				= 	Shift::where('id','=',$shiftId)->where('barber_id', '=', $barber->id)->get();

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
		$validator = Validator::make(Input::all(),[
            'start_time'    =>  'required',
            'end_time' 		=>  'required',
            'time_gap' 		=>  'required',
            'date' 			=>  'required'
        ]);

        if(!$validator->fails()){
        	$user = User::whereUsername($username)->get();
        	if($user->count()){
        		$barber 	=	Barber::where('login_id', '=', $user->first()->id)->get();
        		$date		=	Date::where('date', '=', Input::get('date'))->get();

        		$shift 		=	Shift::find($shiftId);
        		
        		$shift->barber_id	=	$barber->first()->id;
        		$shift->start_time 	=	Input::get('start_time').':00:00';
        		$shift->end_time	=	Input::get('end_time').':00:00';
        		$shift->time_gap	=	(int)Input::get('time_gap');
        		$shift->date_id 	=	$date->first()->id;
        		
        		if($shift->save()){
        			return Response::json([
        				'data'	=>	[
        					'message'		=>	'Shift has been successfully updated.'
        				]
        			]);
        		}
        	}
        	return Response::json([
				'errors' => [
					'message'				=>	'Barber does not exist or Shift cannot be udated.'
				]
			]);
        }
        return Response::json([
			'errors' => [
				'message'				=>	'Validation failed.',
				'errors_details'		=>	$validator->messages()
			]
		]);
	}

	/*
	public function destroy($username, $shiftId)
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
	*/
}