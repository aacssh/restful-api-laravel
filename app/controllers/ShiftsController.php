<?php
use \HairConnect\Transformers\ShiftsTransformer;
use \HairConnect\Services\ShiftCreatorService;
use \HairConnect\Exceptions\ValidationException;

class ShiftsController extends TokensController {
	
	/**
	 * @var ShiftsTransformer
	 */
	protected $transformer;

	/**
	 * @var APIResponse
	 */
	protected $api;

	/**
	 * @var ShiftCreatorService
	 */
	protected $service;

	/**
	 * [__construct description]
	 * @param [type] $shiftsTransformer [description]
	 */
	function __construct(ShiftsTransformer $transformer, APIResponse $api, ShiftCreatorService $service){
		$this->transformer = $transformer;
		$this->api = $api;
		$this->service = $service;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($username)
	{
		if(($barber = $this->checkTokenAndUsernameExists(Input::get('token'), $username)) != false){
			$shift = Shift::where('user_id', '=', $barber->id)->get();

			if($shift->count()){
				return $this->api->respondSuccessWithDetails('Shifts successfully retrieve', $this->transformer->transformCollection($shift->all()));
			}
			return $this->api->respondNoContent('You have not created any shift.');
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($username)
	{
		if($this->checkTokenAndUsernameExists(Input::get('token'), $username) != false){
			try{
				$this->service->make($username, Input::all());
			}catch(ValidationException $e){
				return $this->api->respondInvalidParameters($e->getErrors());	
			}
			return $this->api->respondCreated('Shift has been successfully created.');
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function show($username, $shiftId)
	{
		if(($barber = $this->checkTokenAndUsernameExists(Input::get('token'), $username)) != false){
			$shift = Shift::where('id','=',$shiftId)->where('user_id', '=', $barber->id)->get();

			if($shift->count()){
				return $this->api->respondSuccessWithDetails('Shift successfully retrieved.'.$this->transformer->transform($shift->first()));
			}
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function update($username, $shiftId)
	{
		if($this->checkTokenAndUsernameExists(Input::get('token'), $username) != false){
			try{
				$shift = $this->service->update($username, $shiftId, Input::all());
			}catch(ValidationException $e){
				return $this->api->respondInvalidParameters($e->getErrors());	
			}
			return $this->api->respondSuccessWithDetails('Shift has been successfully updated.', $this->transformer->transform($shift));
		}
		return $this->api->respondInvalidParameters(self::MESSAGE_FOR_INVALID_TOKEN_AND_USERNAME);
	}
}