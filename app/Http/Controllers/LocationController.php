<?php

namespace App\Http\Controllers;

use App\City_US;
use App\Exceptions\Exception;
use App\Http\Resources\CityUSAResource;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Response;
class LocationController extends Controller
{
    /**
    * @SWG\Get(
    *         path="/api/city_us",
    *         tags={"PUBLIC"},
    *         summary="Get list climate region",
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
        $city_us = City_US::orderBy('id','asc')->get();
        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_CITY,
            'data' => CityUSAResource::collection($city_us)
        ],response::HTTP_OK);
        
    }
}
