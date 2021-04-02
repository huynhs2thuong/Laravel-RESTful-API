<?php

namespace App\Http\Controllers;

use App\Climate_region;
use App\Exceptions\Exception;
use App\Http\Resources\Climate_regionResource;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Uuid;


class Climate_regionController extends Controller
{
    /**
    * @SWG\Get(
    *         path="/api/climate_region",
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
    public function index()
    {
        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_CLIMATE,
            'data' => Climate_regionResource::collection(Climate_region::all())
        ],response::HTTP_OK);
        //return Climate_regionResource::collection(Climate_region::all());
    }

    public function store(Request $request)
    {
        $sid = Uuid::generate()->string;
        $data = $request->all();
        $data['sid'] = $sid;
        $climate_region = Climate_region::create($data);
        return response(new Climate_regionResource($climate_region), Response::HTTP_CREATED);
    }
    
    
    
}
