<?php
use \HairConnect\Transformers\ShiftsTransformer;
use \HairConnect\Services\ShiftService;
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
	function __construct(ShiftsTransformer $transformer, APIResponse $api, ShiftService $service){
		$this->transformer = $transformer;
		$this->api = $api;
		$this->service = $service;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($username){
		try{
			$this->service->getAll(Input::all(), $username);
			return $this->api->respondSuccessWithDetails('Shifts successfully retrieve', $this->transformer->transformCollection($this->service->getShiftDetails()));
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($username){
		try{
			$this->service->make(Input::all(), $username);
			return $this->api->respondCreated('Shift has been successfully created.');
		}catch(ValidationException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function show($username, $shiftId){
		try{
			$this->service->show(Input::all(), $username, $shiftId);
			return $this->api->respondSuccessWithDetails('Shifts successfully retrieve', $this->transformer->transform($this->service->getShiftDetails()));
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function update($username, $shiftId){
		try{
			$this->service->update(Input::all(), $username, $shiftId);
			return $this->api->respondCreated('Shift has been successfully updated.');
		}catch(RuntimeException $e){
			return $this->api->respondInvalidParameters($e->getMessage());
		}
	}
}