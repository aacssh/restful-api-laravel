<?php
use \HairConnect\Transformers\ShiftsTransformer;
use \HairConnect\Services\ShiftCreatorService;
use \HairConnect\Validators\ValidationException;

class ShiftsController extends TokensController {
	/**
	 * [$shiftsTransformer description]
	 * @var [type]
	 */
	protected $shiftsTransformer;

	/**
	 * [$apiController description]
	 * @var [type]
	 */
	protected $apiController;

	/**
	 * [$shiftCreator description]
	 * @var [type]
	 */
	protected $shiftCreator;

	/**
	 * [__construct description]
	 * @param [type] $shiftsTransformer [description]
	 */
	function __construct(ShiftsTransformer $shiftsTransformer, APIController $apiController, ShiftCreatorService $shiftCreator){
		$this->shiftsTransformer 	=	$shiftsTransformer;
		$this->apiController 		=	$apiController;
		$this->shiftCreator 		=	$shiftCreator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($username)
	{
		if(($barber = $this->checkToken(Input::get('token'), $username)) != false){
			$shift 	   = 	Shift::where('user_id', '=', $barber->id)->get();

			if($shift->count()){
				return $this->apiController->respond([
					'shifts'	=>	$this->shiftsTransformer->transformCollection($shift->all())
				]);
			}
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token or User/Shift does not exist.'
            ]
        ]);		
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($username)
	{
		if($this->checkToken(Input::get('token'), $username) != false){
			try{
				$this->shiftCreator->make($username, Input::all());
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getErrors());	
			}

			return $this->apiController->respond([
				'message'	=>	'Shift has been successfully created.'
			]);
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token or User/Shift does not exist.'
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
		if(($barber = $this->checkToken(Input::get('token'), $username)) != false){
			$shift = Shift::where('id','=',$shiftId)->where('user_id', '=', $barber->id)->get();

			if($shift->count()){
				return $this->apiController->respond([
					'shifts'	=>	$this->shiftsTransformer->transform($shift->first())
				]);
			}
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token or User/Shift does not exist.'
            ]
        ]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function update($username, $shiftId)
	{
		if($this->checkToken(Input::get('token'), $username) != false){
			try{
				$shift = $this->shiftCreator->update($username, $shiftId, Input::all());
			}catch(ValidationException $e){
				return $this->apiController->respondInvalidParameters($e->getErrors());	
			}

			return $this->apiController->respond([
				'message'	=>	'Shift has been successfully updated.',
				'details'	=>	$this->shiftsTransformer->transform($shift)
			]);
		}
		return $this->apiController->respond([
			'error' => [			
            	'message' => 'Invalid token or User/Shift does not exist.'
            ]
        ]);
	}
}