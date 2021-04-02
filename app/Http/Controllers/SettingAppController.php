<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingAppController extends Controller
{
    /**
    * @SWG\Get(
    *         path="/api/setting",
    *         tags={"PUBLIC"},
    *         summary="Get settings",
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
    public function index(){
        $sql = "
          SELECT *
          FROM settings
        ";
        $obj_list = DB::select($sql);
        
        return response()->json([
            'status' => "Success",
            'data' => $obj_list,
        ],Response::HTTP_OK);
    }
}
