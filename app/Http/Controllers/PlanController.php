<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportTemplate;
use App\Job;
use App\Plan;
use App\Plan_actual;
use Carbon\Carbon;
use App\FCM_token;
use App\Notification;
use App\Store;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
    * @SWG\Put(
    *         path="/api/plans/{sid}",
    *         tags={"ADMIN/PLAN"},
    *         summary="Put a plan by UUID",
    *         @SWG\Parameter(
    *           name="data",
    *           in="body",
    *           default="{}",
    *           required=true,
    *           type="object",
    *           @SWG\Schema(
    *               @SWG\Property(property="reject", type="string", example="1")
    *           ),  
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this plan.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Response(
    *           response=401,
    *           description="Unauthenticated"
    *         ),
    *         @SWG\Response(
    *           response=400,
    *           description="Bad Request"
    *         ),
    *       security={
    *           {"ApiKeyAuth": {}}
    *       },
    * )
    */
    public function update(Request $request, $sid){
        $obj = Plan::whereSid($sid)->first();
        $data = $request->all();
		$flag_reject = !empty($data['reject']);
		if($flag_reject){
			// Update status of Plan
			$data = array(
                'status' => 'INPROGRESS',
                'is_reject' => 1,
			);
		}
		$obj->update($data);
		
		if($flag_reject){
            $noti = new Notification;
			$plan_actual = Plan_actual::wherePlanSid($sid)->first();
			if(!empty($plan_actual)){
                $plan_actual_sid = $plan_actual['sid'];
				// $deviceTokens = FCM_token::whereEmployeeSid($obj->employee_sid)->get();
                // $noti->toMultiDevice($deviceTokens,'Store reject','You are assigned a new job. Please check again.');			
				// Update job
                $job = Job::whereSid($obj->job_sid)->first();
                $store = Store::whereSid($obj->store_sid)->first();
                $deviceTokens = FCM_token::whereEmployeeSid($obj->employee_sid)->get();
                $title = 'Store '.$store->name.' in job '.$job->name.' reject. Please check again.';
                $noti->toMultiDevice($deviceTokens,'Store reject',$title);
				if(!empty($job->sid)){
					$store_completed_count = 0;
					$store_in_process_count = 0;
					$query = "
						SELECT COUNT(*) AS count_store
						FROM 
							plans 
							INNER JOIN stores on plans.store_sid = stores.sid 
						WHERE plans.job_sid = '{$obj->job_sid}' AND plans.status = 'DONE'
					";
					// var_dump($query); die();
			
					$obj_query = DB::select($query);
					if(!empty($obj_query)){
						$store_completed_count = $obj_query[0]->count_store;
					}
					
					$query = "
						SELECT COUNT(*) AS count_store
						FROM 
							plans 
							INNER JOIN stores on plans.store_sid = stores.sid 
						WHERE plans.job_sid = '{$obj->job_sid}' AND plans.status = 'INPROGRESS'
					";
					// var_dump($query); die();
			
					$obj_query = DB::select($query);
					if(!empty($obj_query)){
						$store_in_process_count = $obj_query[0]->count_store;
					}

					$job_update = array(
						'status' => 'INPROGRESS',
						'store_completed_count' => $store_completed_count,
						'store_in_process_count' => $store_in_process_count,
					);
					$job->update($job_update);
				}
			}
        }
        $activiti = array();
        $auth = \Auth::user();
       

        $activiti['description'] = 'Upload test data Plan';
        $activiti['subject_type'] = 'App\Plan';
        $activiti['causer_sid'] = $auth->sid;
        $activiti['causer_id'] = $auth->id;
        $activiti['log_name'] = $auth->username;
        Activity::create($activiti);

        return response()->json([
            'status' => "Success",
            'message' => Exception::UPDATED_PLAN_SUCCESS,
            'data' => $data
        ],Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    /**
    * @SWG\Post(
    *         path="/api/export_data",
    *         tags={"ADMIN/EXPORT"},
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *                   @SWG\Property(property="job_sid", type="string", example="string"),
    *                   @SWG\Property(property="plan_sid", type="string", example="string"),
    *               ),
    *         ),
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Response(
    *           response=401,
    *           description="Unauthenticated"
    *         ),
    *         @SWG\Response(
    *           response=400,
    *           description="Bad Request"
    *         ),
    *       security={
    *           {"ApiKeyAuth": {}}
    *       },
    * )
    */
    public function exportExcel(Request $request) {
        
        $date = Carbon::now('Asia/Ho_Chi_Minh');
        
        $date = date('Y_m_d_H_i_s', strtotime($date));
        $export_file_name = "Report".str_replace("export", "", "")."_{$date}.xlsx";
        $params = $request->all();
        $data = Excel::store(new ReportTemplate($params), $export_file_name, 'customer_report');
        $fullPath = 'export_data/' . $export_file_name;
        if($data == 1){
            return response()->json([
                'status' => "Success",
                'message' => Exception::EXPORT_SUCCESS,
                'data' => env('APP_URL').'file/'.$fullPath,
            ],Response::HTTP_OK);
        }else{
            return response()->json(
                Exception::exportFailed(),400);
        }
    }
}
