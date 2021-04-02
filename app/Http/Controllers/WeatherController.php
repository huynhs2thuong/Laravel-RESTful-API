<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\WeatherResource;
use App\Weather;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JWTAuth;

class WeatherController extends Controller
{
    /**
    * @SWG\Get(
    *         path="/api/weather_status",
    *         tags={"PUBLIC"},
    *         summary="Get list weather-status",
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
        $weather = Weather::orderBy('id','asc')->get();
        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_WEATHER,
            'data' => WeatherResource::collection($weather)
        ],Response::HTTP_OK);
        
    }
}
