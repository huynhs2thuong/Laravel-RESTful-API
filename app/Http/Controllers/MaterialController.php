<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialResource;
use App\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Uuid;

class MaterialController extends Controller
{

    /**
    * @SWG\Get(
    *         path="/api/materials",
    *         tags={"PUBLIC"},
    *         summary="Get list materials",
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

    public function __construct()
    {
        
    }

    public function index() {
        $result = null;
        $material = Material::orderBy('id','asc');
        // $page_size = $request->page_size;
        $page_size  = 0; // TODO
        if(!empty($page_size)){
            $result = $material->paginate();
        } else {
            $result = $material->get();
        }
        
        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_MATERIAL,
            'data' => MaterialResource::collection($result)
        ],Response::HTTP_OK);
        
    }

    public function mass_create(){
        $this->user = JWTAuth::parseToken()->authenticate();
        if($this->user){
            $input_arr = array(
				'PAINTED SPLIT CMU',
				'UNPAINTED SPLIT CMU',
				'PAINTED SMOOTH CMU',
				'UNPAINTED SMOOTH CMU',
				'PAINTED 8X8" SMOOTH CMU',
				'PAINTED 8X8" SPLIT CMU',
				'UNPAINTED 8X8" SMOOTH CMU',
				'UNPAINTED 8X8" SPLIT CMU',
				'PAINTED WOOD',
				'UNPAINTED WOOD',
				'TILT UP COBBLE',
				'TILT UP CEMENT',
				'METAL PANEL',
				'CLEAR COAT NORMAL BRICK',
				'PAINTED NORMAL BRICK',
				'EIFS',
				'PAINTED FINNED BRICK ',
				'UNPAINTED FINNED BRICK',
			);
			foreach($input_arr as $name){
				// Check duplicate
                $check_obj = Material::whereName($name)->first();
                if(empty($check_obj)){
					$sid  = Uuid::generate()->string;
					$obj = array();
					$obj['sid'] = $sid;
					$obj['code'] = $name;
					$obj['name'] = $name;
					$obj['is_active'] = 1;
					Material::create($obj);
                }
			}
        }
    }
}
