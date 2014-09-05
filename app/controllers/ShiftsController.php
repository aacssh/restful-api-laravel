<?php

class ShiftsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($username)
	{
		$log		=	User::whereUsername($username)->get();
		if($log->count()){
			$barber	=	Barber::where('login_id', '=', $log->first()->id)->get();
			if($barber->count()){
				$barber = $barber->first();
				$shift 	= Shift::where('barber_id', '=', $barber->id)->get();

				if($shift->count()){
					return $shift;
				}
				
			}
		}

		
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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