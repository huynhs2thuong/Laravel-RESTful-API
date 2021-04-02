<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Response;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{


    /**
    * @SWG\Get(
    *         path="/api/activities",
    *         tags={"ADMIN/ACTIVITY"},
    *         summary="Get list activities",
    *         @SWG\Parameter(
    *             name="page",
    *             description="Pagination page",
    *             in="query",
    *             type="integer"
    *         ),
    *          @SWG\Parameter(
    *             name="search_username",
    *             description="Search Activities",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="page_size",
    *             description="Number of results to return per page.",
    *             in="query",
    *             type="integer"
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
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
        $page_size = $request->page_size;
        if(empty($page_size)){
            $page_size = 15;

        }
        $activity_log = Activity::orderBy('id','desc')->join('employees', 'activity_log.causer_sid', '=', 'employees.sid')->select('activity_log.id','activity_log.log_name','activity_log.description','activity_log.subject_type','activity_log.causer_sid','activity_log.created_at','activity_log.updated_at','employees.sid AS employee_sid','employees.username AS employee_username','employees.avatar AS employee_avatar');
        $search = $request->search_username;
        if(!empty($search)){
            $result = $activity_log->where('employees.username', 'LIKE','%'.$search.'%' )->orderBy('id','desc')->paginate($page_size);;
            //return ActivityResource::collection($activity_log);
        }
        $result = $activity_log->orderBy('id','desc')->paginate($page_size);
       return ActivityResource::collection($result);
       
    }

    /**
    * @SWG\Get(
    *         path="/api/activity/employee/{sid}",
    *         tags={"ADMIN/ACTIVITY"},
    *         summary="Get list activities of employee",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this employee.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
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
    public function filter_activity_employee($sid){
        $activity_log = Activity::where('causer_sid',$sid)->join('employees', 'activity_log.causer_sid', '=', 'employees.sid')->select('activity_log.id','activity_log.log_name','activity_log.description','activity_log.subject_type','activity_log.causer_sid','activity_log.created_at','activity_log.updated_at','employees.sid AS employee_sid','employees.username AS employee_username','employees.avatar AS employee_avatar')->orderBy('id','desc')->get();
        // return ActivityResource::collection($activity_log);
        return response()->json([
            'status' => "Success",
            'message' => Exception::SHOW_ACTIVITY,
            'data' => ActivityResource::collection($activity_log),
        ],HttpResponse::HTTP_OK);
    }

    /**
    * @SWG\Get(
    *         path="/api/mobile/activities",
    *         tags={"MOBILE/ACTIVITY"},
    *         summary="Get list activity of employee",
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="page",
    *             description="Pagination page",
    *             in="query",
    *             type="integer"
    *         ),
    *         @SWG\Parameter(
    *             name="page_size",
    *             description="Number of results to return per page.",
    *             in="query",
    *             type="integer"
    *         ),
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

    function show(Request $request){
        $auth = \Auth::user();
        $sid_mb = $auth->sid;
        $page_size = $request->page_size;
        if(empty($page_size)){
            $page_size = 15;

        }
        $activity_log = Activity::where('causer_sid',$sid_mb)->join('employees', 'activity_log.causer_sid', '=', 'employees.sid')->select('activity_log.id','activity_log.log_name','activity_log.description','activity_log.subject_type','activity_log.causer_sid','activity_log.created_at','activity_log.updated_at','employees.sid AS employee_sid','employees.username AS employee_username','employees.avatar AS employee_avatar')->orderBy('id','desc')->paginate($page_size);
        return ActivityResource::collection($activity_log);
        
    }
    
    
}