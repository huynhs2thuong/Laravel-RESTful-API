<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VersionAppController extends Controller
{
    /**
    * @SWG\Get(
    *         path="/api/app_version",
    *         tags={"PUBLIC"},
    *         summary="Get version app",
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
        $version = array('version_android' => env('APP_VERSION_ANDROID', false), 'version_ios' => env('APP_VERSION_IOS', false));
        return response()->json([
            'status' => "Success",
            'data' => $version
        ],response::HTTP_OK);
    }

    public function testwedigi(){
        throw new \Illuminate\Http\Exceptions\ThrottleRequestsException();
    }
    public function keep_session(){
        
    }
}
