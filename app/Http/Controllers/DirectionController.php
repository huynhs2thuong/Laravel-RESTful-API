<?php

namespace App\Http\Controllers;

use App\Direction;
use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\DirectionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpParser\Node\Scalar\MagicConst\Dir;
use Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;

class DirectionController extends Controller
{
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
    * @SWG\Get(
    *         path="/api/cardinal_direction",
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
    public function index(Request $request){
        $direction = Direction::orderBy('id','desc')->paginate($request->page_size);
        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_DIRECTION,
            'data' => DirectionResource::collection($direction)
        ],response::HTTP_OK);
        
    }


    public function show($id){

        $direction = Direction::Where('sid',$id)->first();
        return response()->json([
            'status' => "Success",
            'data' => new DirectionResource($direction),
        ],Response::HTTP_OK);
    }

    

    public function store(Request $request)
    {

        $sid = Uuid::generate()->string;

        $direction = Direction::create($request->only('name','is_active')+['sid'=>$sid]);

        
        return response(new DirectionResource($direction), Response::HTTP_CREATED);
    }


    public function update(Request $request, $id){

        $direction = Direction::whereSid($id)->first();
        
        $data = $request->all();
        
        $direction->update($data);
        
       return response(new DirectionResource($direction), Response::HTTP_ACCEPTED);
    
    }

    public function destroy($sid){

        $item = Direction::where('sid', $sid)->first();
        
        if($item->is_active == 1){
            $item->update(['is_active' => 0]);
            return response()->json([
                'status' => true,
                'message' => Exception::DELETED_DIRECTION_SUCCESS,
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => Exception::DELETED_DIRECTION_FAILED,
            ]);
        }
    }



}
