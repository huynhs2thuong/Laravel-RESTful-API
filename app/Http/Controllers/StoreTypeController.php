<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreTypeResource;
use App\StoreType;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Uuid;

class StoreTypeController extends Controller
{
    /**
    * @SWG\Get(
    *         path="/api/store_type",
    *         tags={"PUBLIC"},
    *         summary="Get list stores type",
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
            'message' => Exception::LIST_STORETYPE,
            'data' => StoreTypeResource::collection(StoreType::all())
        ],response::HTTP_OK);
        //return StoreTypeResource::collection(StoreType::all());
    }

    public function store(Request $request)
    {

        $sid = Uuid::generate()->string;

        $storetype = StoreType::create($request->only('name','code','is_active')+['sid'=>$sid]);

        
        return response(new StoreTypeResource($storetype), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id){

        $storetype = StoreType::whereSid($id)->first();
        
        $data = $request->all();
        
        $storetype->update($data);
        
       return response(new StoreTypeResource($storetype), Response::HTTP_ACCEPTED);
    
    }

    public function destroy($sid){

        $item = StoreType::where('sid', $sid)->first();
        
        if($item->is_active == 1){
            $item->update(['is_active' => 0]);
            return response()->json([
                'status' => true,
                'message' => Exception::DELETED_STORETYPE_SUCCESS,
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => Exception::DELETED_STORETYPE_FAILED,
            ]);
        }
    }
}
