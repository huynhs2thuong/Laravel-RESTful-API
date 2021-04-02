<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimeOfDayResource;
use App\TimeOfDay;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JWTAuth;

class TimeOfDayController extends Controller
{
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
    * @SWG\Get(
    *         path="/api/time_of_day",
    *         tags={"PUBLIC"},
    *         summary="Get list Time of Day of Evaluation",
    *         @SWG\Response(
    *             response=200,
    *             description="Successful operation"
    *         ),

    *         @SWG\Response(
    *             response=500,
    *             description="Server error"
    *         ),
    *   security={{"ApiKeyAuth":{}}},
    * )
    */
    public function index() {
        $time = TimeOfDay::orderBy('id','asc')->get();
        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_TIMEOFDATE,
            'data' => TimeOfDayResource::collection($time)
        ],Response::HTTP_OK);
        
    }
}
