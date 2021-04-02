<?php

namespace App\Http\Controllers;

use App\Company;
use App\Store;
use App\Employee_to_company;
use App\Exceptions\Exception;
use App\Job;
use App\Job_to_Territory;
use App\Territory;
use App\Plan;
use App\Plan_actual;
use App\Plan_data;
use App\JsonStructure;
use Spatie\Activitylog\Models\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobCreateRequest;
use App\Http\Resources\JobREsource;
use App\Http\Resources\StoreResource;
use App\Http\Resources\JsonStructoreResource;


use App\FCM_token;
use App\Http\Resources\ListPhotoResources;
use App\Http\Resources\PhotoResources;
use App\Notification;
use App\Photo;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Uuid;

class JobController extends Controller
{
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
     /**
    * @SWG\Get(
    *         path="/api/jobs",
    *         tags={"ADMIN/JOB"},
    *         summary="Get list JOB",
    *         @SWG\Parameter(
    *             name="company_sid",
    *             description="company_sid",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="status",
    *             description="status",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="search",
    *             description="Search job",
    *             in="query",
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
    public function index(Request $request)
    {
        $jobs = Job::orderBy('id','desc')->paginate($request->page_size);
        $search = $request->search;
        $company_sid = $request->company_sid; 
        $status = $request->status;

        $su = $search.$company_sid.$status;

        if(!empty($su)){
                    $search_job = Job::Where(function($query)  use ($search) {
                                            $query->where('name','LIKE', '%'.$search.'%')
                                            ->orWhere('code','LIKE', '%'.$search.'%');
                                        })
                                     ->where('company_sid','LIKE', '%'.$company_sid.'%')
                                     ->Where('status','LIKE', '%'.$status.'%')
                                    ->paginate();
            return JobREsource::collection($search_job);
        }

         return JobREsource::collection($jobs);
    }
    /**
    * @SWG\Get(
    *         path="/api/jobs/{sid}",
    *         tags={"ADMIN/JOB"},
    *         summary="Get a Job by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this job.",
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
    public function show($sid){

        $job = Job::Where('sid',$sid)->select('sid','code','name','status','company_sid')->first();
        // $job_to_territories = Job_to_Territory::where('job_sid',$sid)->where('company_sid',$job->company_sid)->get();
        // $data = [];
        // foreach($job_to_territories as $item){
        //     $territory = Territory::whereSid($item->territory_sid)->select('sid','code','name')->first();
        //     array_push($data,$territory);
        // }
        // $company = Company::whereSid($job->company_sid)->select('sid','name')->first();
        // $job['company'] = $company;
        // $job['territory'] = $data;
        return response()->json([
            'status' => "Success",
            'message' => Exception::SHOW_JOB,
            'data' => new JobREsource($job)
        ],Response::HTTP_OK);
        //return $job;
    }

    private function data_check($params){ // Assume $flag_change = true
        $return_data = null;

        $action = $params['action'];
        $input_data = $params['input_data'];
        // $flag_change = $params['flag_change'];
		// echo json_encode($input_data);die();
        if($action == 'create' || $action == 'update'){ // Applied for these actions
            $company_sid = $input_data['company_sid'];
			
            // Check duplicate data
            $territories_sid = $input_data['territories_sid'];
			
			// Check empty data
			foreach($territories_sid as $territory_sid){
				$table_mapping = array(
					'employee_to_companies' => 'employee',
					'stores' => 'store',
				);
				foreach($table_mapping as $table => $text){
					$condition = "company_sid = '{$company_sid}' AND territory_sid = '{$territory_sid}'";
					
					$sql = "
						SELECT count(*) AS count_total
						FROM {$table} 
						WHERE {$condition} AND is_active = 1
					"; // Asume company_sid, territory_sid, [PLACEHODLER], is_active is unique
					// die($sql);
					$query_list = DB::select($sql);
					$count_total = 0;
					foreach($query_list as $query_obj){
						$count_total = $query_obj->count_total;
					}
					if($count_total == 0){
						$return_data = response()->json([
							'status' => 'Failed',
							'message' => "Please check that at least 1 {$text} is assigned to selected companies and territories",
						],400);
						break; // End loop
					}
				}
			}
			
        } else if($action == 'delete'){
            // $company_sid = $input_data['company_sid'];
            // $territory_sid = $input_data['territory_sid'];
            // $employee_sid = $input_data['employee_sid'];
            // Step 1: Check related jobs
            // $condition = "company_sid = '{$company_sid}' AND territory_sid = '{$territory_sid}' AND employee_sid = '{$employee_sid}'";
            // $sql = "
                // SELECT count(*) AS count_total
                // FROM plans WHERE {$condition}
            // ";
            // $query_list = DB::select($sql);
            // $count_total = 0;
            // foreach($query_list as $query_obj){
                // $count_total = $query_obj->count_total;
            // }
            // if($count_total > 0){
                // $return_data = response()->json([
                    // 'status' => 'Failed',
                    // 'message' => 'Please unassign related jobs before doing deletion.',
                // ],400);
            // }

            // Step 2: Check related plans => No need
        }

        return $return_data;
    }

    /**
    * @SWG\Post(
    *         path="/api/jobs",
    *         tags={"ADMIN/JOB"},
    *         summary="Create a Job",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               required={"code","name","territories_sid","company_sid"},
    *               @SWG\Property(property="code", type="string", example="string"),
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="territories_sid", type="array",@SWG\Items(type="string")),
    *               @SWG\Property(property="company_sid", type="string", example="string"),
    *               @SWG\Property(property="status",enum={"NEW", "INPROGRESS","COMPLETED","INACTIVE","REJECT"} ,type="string", example="NEW"),
    *               @SWG\Property(property="push_notification", type="string", example=true),
    *          ),
    *               
    *      ),
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
    public function store(JobCreateRequest $request){
        $username = Job::whereName($request->name)->whereCompanySid($request->company_sid)->first();
        if(!empty($username)){
            $return_data = response()->json(
                Exception::job_name_duplicated()
                ,400);
        }

        // TODO - Validate
        $data = $request->all(); // TODO - Validate
		$input_data = $data;
        $params['input_data'] = $input_data;
        $params['action'] = 'create';

        if(empty($return_data)){
            $return_data = $this->data_check($params); // Alway check this data
        }
        if(empty($return_data)){

            $job_sid = Uuid::generate()->string;
            $company_sid = $request->company_sid;
            $territories_sid = $request->territories_sid;
            // var_dump($territories_sid);die();
            $flag_push_notification = false;
            $data['push_notification'] = 0;
            if($request->push_notification == 'true'){
                $flag_push_notification = true;
            
                $noti = new Notification;
                
                //return $data;
            // $deviceTokens = FCM_token::whereEmployeeSid('d8232650-199b-11eb-af06-f5c15aba92e0')
            //  ->pluck('fcm_token')
            // ->toArray();
            //$token = 'ditQiJ-oXkI6OUvT-shds8:APA91bGu3PKtLWvFJ_7sHXElP4S2-I_hB_mRxb210fasWwPWCvrQuM5-kAaQhgKlBDERJ7x9x4osM7bYLmxeO_X_5fxeaowUz_XKZRHqh8yfSZ1rbukte-Swg8feN81yQdZrvdqP4BEZ';
            // $noti = new Notification;
            // $noti->toSingleDevice($deviceTokens,'title','body');
            }else{
                // Do nothing
            }
            if($flag_push_notification){
                $data['push_notification'] = 1;
                $noti = new Notification;
            }
            $store_count = 0;
            $employee_count = 0;
            foreach($territories_sid as $territory_sid) {
                $employees_sid = Employee_to_company::whereCompanySid($company_sid)->whereTerritorySid($territory_sid)->get();
                $stores_sid = Store::whereCompanySid($company_sid)->whereTerritorySid($territory_sid)->get();
                if(empty($employees_sid)){
                    return response()->json(
                        Exception::noEmployee()
                    ,400);
                }
                if(empty($stores_sid)){
                    return response()->json(
                        Exception::noStore(),400);
                }
                $employee_count += count($employees_sid);
                $store_count += count($stores_sid);
                
                
                //var_dump($stores_sid);die();
                foreach($employees_sid as $obj){
                    $employee_sid = $obj->employee_sid;
                    
                    // Push notification
                    if(!empty($noti)){
                        $deviceTokens = FCM_token::whereEmployeeSid($employee_sid)->get();
                        $noti->toMultiDevice($deviceTokens,'New job','You are assigned a new job. Please check again.');
                    }

                    foreach($stores_sid as $store_obj){
                        $store_sid = $store_obj->sid;
                        // Create plan
                        $plan_sid = Uuid::generate()->string;
                        DB::table('plans')->insert([
                            'sid' => $plan_sid,
                            'code' => '',
                            'is_manual' => 0,
                            'company_sid' => $company_sid,
                            'territory_sid' => $territory_sid,
                            'store_sid' => $store_sid,
                            'job_sid' => $job_sid,
                            'employee_sid' => $employee_sid,
                            
                            'status' => 'NEW',
                            'is_active' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }

                }
            }
            if($employee_count == 0){
                return response()->json(
                    Exception::selectEmployee(),400);
            }
            if($store_count == 0){
                return response()->json(
                    Exception::selectStore(),400);
            }
            $data['store_count'] = $store_count;



            $data['sid'] = $job_sid;
            $job = Job::create($data);
            if(!empty($job)){
                foreach($territories_sid as $territory_sid) {
                    DB::table('job_to_territories')->insert([
                        'job_sid' => $job_sid,
                        'territory_sid' => $territory_sid,
                        'company_sid' => $company_sid,
                        'status' =>true
                    ]);
                }
            }
            $job_to_territories = Job_to_Territory::where('job_sid',$job_sid)->where('company_sid',$company_sid)->get();
            $data = [];
            foreach($job_to_territories as $item){
                $territory = Territory::whereSid($item->territory_sid)->select('sid','code','name')->first();
                array_push($data,$territory);
            }
            $company = Company::whereSid($company_sid)->select('sid','name')->first();
            $job['company'] = $company;
            $job['territory'] = $data;
        //$job = Job::orderBy('id','desc')->join('companies', 'jobs.company_sid', '=', 'companies.sid')->get(['jobs.id','jobs.sid','jobs.code','jobs.name','jobs.status','jobs.company_sid','companies.name AS company_name']);
            // $job = Job::create($request->only('name', 'territorie_sid', 'company_sid', 'employee_sid', 'is_active') + ['sid' => $job_sid]);
            $jobs = Job::orderBy('id','desc')->get();
            return response()->json([
                'status' => "Success",
                'message' => Exception::CREATED_JOB_SUCCESS,
                'data' => $job
            ],Response::HTTP_CREATED);
        }

        return $return_data;
    }
    /**
    * @SWG\Put(
    *         path="/api/jobs/{sid}",
    *         tags={"ADMIN/JOB"},
    *         summary="Put a Job by UUID",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *               @SWG\Property(property="code", type="string", example="string"),
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="company_sid", type="string", example="string"),
    *               @SWG\Property(property="territories_sid", type="array",@SWG\Items(type="string")),
    *               @SWG\Property(property="status", type="string",enum={"NEW", "INPROGRESS","COMPLETED","INACTIVE","REJECT"}, example="string")
    *               ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this job.",
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

    private function update_check($sql){
        $result = false;
        $obj_query = DB::select($sql);
        foreach($obj_query as $obj){
            if(!empty($obj)){
                $result = true;
                break;
            }
        }
        return $result;
    }
    public function update(Request $request, $sid){
        $return_data = null;
        $company_sid = $request->company_sid;
        $territories_sid = $request->territories_sid;
        //var_dump($territories_sid);die;
        $job = Job::whereSid($sid)->first();
        $data = $request->all();
        $flag_update = false;
        // var_dump($data);exit();
        if(!empty($data['status'])){
            $status = $data['status'];
            $status_old = $job->status;
            // echo '$status = '.$status;
            // echo '<br>';
            // echo '$status_old = '.$status_old;
            // die();
            if($status==$status_old){
                $flag_update = true; // No update to status
            } 
            else if($status_old=='NEW'){
                if($status=='INACTIVE'){
                    $flag_update = true;
                }
            }
            else if($status_old=='INACTIVE'){
                if($status=='NEW'){
                    // All plans must be NEW
                    $sql = "
						SELECT *
						FROM plans
						WHERE plans.job_sid = '{$sid}' AND plans.status <> 'NEW'
					";
                    $flag_check = $this->update_check($sql);
                    if($flag_check){
                        $return_data = response()->json(
                            Exception::storeMustNew(),400);
                    } else {
                        $flag_update = true;
                    }
                }
                else if($status=='INPROGRESS'){
                    $flag_update = true; // TODO: IN PROCESS - Old move is from IN PROGRESS to INACTIVE?
                }
            }
            else if($status_old=='INPROGRESS'){
                if($status=='INACTIVE'){
                    $flag_update = true;
                }
                else if($status=='DONE'){
                    // All plans must be DONE
                    $sql = "
						SELECT *
						FROM plans
						WHERE plans.job_sid = '{$sid}' AND plans.status <> 'DONE'
					";
                    $flag_check = $this->update_check($sql);
                    if($flag_check){
                        $return_data = response()->json(
                            Exception::storeMustDone(),400);
                    } else {
                        $flag_update = true;
                    }
                }
            }
            else if($status_old=='DONE'){
                // Not allow any move
            }
        } else {
            $flag_update = true; // Default: allow update
        }
        if($flag_update){
            $job->update($data);
            $territories_request=[];
            $job_to_territories = DB::table('job_to_territories')->where('job_sid', $sid)->where('company_sid', $company_sid)->get();
            $job_to_territories = json_decode($job_to_territories, true);
            foreach($job_to_territories as $item) {
                array_push($territories_request,$item["territory_sid"]);
            }
            $data_territories= [];
            $data_territories=array_diff($territories_sid,$territories_request);
            if(!empty($data_territories)){
                foreach($data_territories as $territory_sid) {
                    DB::table('job_to_territories')->insert([
                        'job_sid' => $sid,
                        'territory_sid' => $territory_sid,
                        'company_sid' => $company_sid,
                        'status' =>true
                    ]);
                }
            }
            $return_data = response()->json([
                'status' => "Success",
                'message' => Exception::UPDATED_INFO_SUCCESS,
                'data' => new JobREsource($job)
            ],Response::HTTP_ACCEPTED);
        } else {
            if(empty($return_data)){
                $return_data = response()->json(
                    Exception::notAllow(),400);
            }
        }

        // $job_to_territories = Job_to_Territory::where('job_sid',$sid)->where('company_sid',$job->company_sid)->get();
        // $data = [];
        // foreach($job_to_territories as $item){
        //     $territory = Territory::whereSid($item->territory_sid)->select('sid','code','name')->first();
        //     array_push($data,$territory);
        // }
        // $company = Company::whereSid($job->company_sid)->select('sid','name')->first();
        // $job['company'] = $company;
        // $job['territory'] = $data;

        return $return_data;
    }

    /**
    * @SWG\Get(
    *         path="/api/mobile/job/{sid}/store",
    *         tags={"MOBILE/JOB"},
    *         summary="Get list Stores of Job",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this Job.",
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
    public function job_to_stores_mobile($sid,Request $request){
        $obj_list = DB::select(DB::raw("
            SELECT 
            plans.sid AS plan_sid, plans.status,
            stores.id, stores.sid, stores.name ,stores.phone, stores.climate_region_sid, stores.store_type_sid, stores.address_1, stores.address_2, stores.city, stores.state, stores.zip_code, stores.img_store, stores.file_store, stores.a2_file_number, stores.a2_day_on_file, stores.opening_hour,stores.is_active, stores.company_sid, stores.territory_sid, stores.created_at,stores.updated_at,
            CONVERT(stores.name,UNSIGNED INTEGER) AS name_number
            FROM 
                plans 
                INNER JOIN stores on plans.store_sid = stores.sid 
            WHERE plans.is_active = 1 AND stores.is_active = 1 AND
                plans.job_sid = '{$sid}'
            ORDER BY name_number ASC, stores.name ASC
        "));
        // $store_mapping = array();
        // foreach($obj_list as $obj){
        //     $store_id = $obj->id;
        //     if(empty($store_mapping[$store_id])){
        //         $store_mapping[$store_id] = $obj;
        //     }
        // }
        // $result = array_values ($store_mapping);
        $result = $obj_list;
        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_STORE_JOB,
            'data' => StoreResource::collection($result)
        ],Response::HTTP_OK);
    }

    /**
    * @SWG\Get(
    *         path="/api/job/{sid}/store",
    *         tags={"ADMIN/JOB"},
    *         summary="Get list Stores of Job",
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
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this Job.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *          @SWG\Parameter(
    *             name="search",
    *             description="Search name store",
    *             in="query",
    *             type="string"
    *         ),
    *          @SWG\Parameter(
    *             name="status",
    *             description="Status store",
    *             in="query",
    *             type="string"
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
    public function job_to_stores($sid,Request $request){
		//    $store = DB::select('
		//     select stores.id, stores.sid, stores.name ,stores.phone, stores.climate_region_sid, stores.store_type_sid, stores.address_1, stores.address_2, stores.city, stores.state, stores.zip_code, stores.img_store, stores.file_store, stores.a2_file_number, stores.a2_day_on_file, stores.opening_hour,stores.is_active,stores.company_sid, stores.territory_sid,stores.created_at,stores.updated_at 
		//     from stores 
		//     inner join job_to_territories on stores.territory_sid = job_to_territories.territory_sid 
		//     inner join companies on stores.company_sid = companies.sid 
		//     where stores.territory_sid = job_to_territories.territory_sid 
        //     and job_to_territories.job_sid = "' .$sid.'"');
        $page_size = $request->page_size;
        if(empty($page_size)){
            $page_size = 15;
        }
        $is_search_filter = false;
        $search = $request->search;
        $status = $request->status;
        $select_custom = DB::raw('CONVERT(stores.name,UNSIGNED INTEGER) AS name_number');
        $select_sql = array($select_custom, 'plans.sid AS plan_sid', 'plans.status', 'stores.id', 'stores.sid', 'stores.name', 'stores.phone', 'stores.climate_region_sid', 'stores.store_type_sid', 'stores.address_1', 'stores.address_2', 'stores.city', 'stores.state', 'stores.zip_code', 'stores.img_store', 'stores.file_store', 'stores.a2_file_number', 'stores.a2_day_on_file', 'stores.opening_hour','stores.is_active', 'stores.company_sid', 'stores.territory_sid', 'stores.created_at','stores.updated_at');
        $listStore = DB::table('plans')
            ->join('stores', 'plans.store_sid', '=', 'stores.sid')
            ->select($select_sql)
            // ->where('stores.is_active', 1)
            // ->where('plans.is_active', 1)
            ->where('plans.job_sid', $sid);
            

        if(!empty($search)){
            $is_search_filter = true;
            $listStore->where('stores.name','LIKE','%'.$search.'%');
        }
        if(!empty($status)){
            $is_search_filter = true;
            $listStore->where('plans.status',$status);
        }
        if($is_search_filter === false){
           $list = DB::table('plans')
            ->join('stores', 'plans.store_sid', '=', 'stores.sid')
            ->select($select_sql)
            // ->where('stores.is_active', 1)
            // ->where('plans.is_active', 1)
            ->where('plans.job_sid', $sid);
            $result = $list->orderBy('name_number','asc')->paginate($page_size);
            return StoreResource::collection($result);
        }else{
            $result = $listStore->orderBy('name_number','asc')->paginate($page_size);
            return StoreResource::collection($result);
        }
        // if(!empty($search)){
        //         $obj_list = DB::select("
        //         SELECT 
        //         plans.sid AS plan_sid, plans.status,
        //         stores.id, stores.sid, stores.name ,stores.phone, stores.climate_region_sid, stores.store_type_sid, stores.address_1, stores.address_2, stores.city, stores.state, stores.zip_code, stores.img_store, stores.file_store, stores.a2_file_number, stores.a2_day_on_file, stores.opening_hour,stores.is_active, stores.company_sid, stores.territory_sid, stores.created_at,stores.updated_at 
        //         FROM 
        //             plans 
        //             INNER JOIN stores on plans.store_sid = stores.sid 
        //         WHERE plans.is_active = 1 AND stores.is_active = 1 AND
        //             plans.job_sid = '{$sid}' and plans.store_sid = '{$search}'
        //     ");
        //     $store_mapping = array();
        //     foreach($obj_list as $obj){
        //         $store_id = $obj->id;
        //         if(empty($store_mapping[$store_id])){
        //             $store_mapping[$store_id] = $obj;
        //         }
        //     }
        //     return response()->json([
        //         'status' => "Success",
        //         'message' => Exception::LIST_STORE_JOB,
        //         'data' => StoreResource::collection(array_values ($store_mapping))
        //     ],Response::HTTP_OK);
        // }

		// $obj_list = DB::select("
		// 	SELECT 
		// 	plans.sid AS plan_sid, plans.status,
		// 	stores.id, stores.sid, stores.name ,stores.phone, stores.climate_region_sid, stores.store_type_sid, stores.address_1, stores.address_2, stores.city, stores.state, stores.zip_code, stores.img_store, stores.file_store, stores.a2_file_number, stores.a2_day_on_file, stores.opening_hour,stores.is_active, stores.company_sid, stores.territory_sid, stores.created_at,stores.updated_at 
		// 	FROM 
		// 		plans 
		// 		INNER JOIN stores on plans.store_sid = stores.sid 
        //     WHERE plans.is_active = 1 AND stores.is_active = 1 AND
        //         plans.job_sid = '{$sid}'
		// ");
		// $store_mapping = array();
		// foreach($obj_list as $obj){
		// 	$store_id = $obj->id;
		// 	if(empty($store_mapping[$store_id])){
		// 		$store_mapping[$store_id] = $obj;
		// 	}
        // }
        // return response()->json([
        //     'status' => "Success",
        //     'message' => Exception::LIST_STORE_JOB,
        //     'data' => StoreResource::collection(array_values ($store_mapping))
        // ],Response::HTTP_OK);
    }

    /**
    * @SWG\Get(
    *         path="/api/mobile/job/plan/{sid}/photo",
    *         tags={"MOBILE/JOB"},
    *         summary="Get list Photos of Job",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying the plan of this Job.",
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
    /**
    * @SWG\Get(
    *         path="/api/job/plan/{sid}/photos",
    *         tags={"ADMIN/JOB"},
    *         summary="Get list Photos of Job",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying the plan of this Job.",
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
    public function plan_to_photos($sid){
        $query = "
            SELECT 
            plans.sid AS plan_sid, 
            photo.id, photo.sid, photo.img_photo, photo.elevation_code, photo.name, photo.description,
            photo_job_tag.photo_tag_sid
            FROM 
                plans 
                INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid 
                INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid 
                INNER JOIN photo_job_tag ON photo.sid = photo_job_tag.photo_sid 
            WHERE plans.sid = '{$sid}'
        ";
        // var_dump($query); die();

		$obj_list = DB::select($query);
		$photo_mapping = array();
		foreach($obj_list as $obj){
            $photo_id = $obj->id;
            $photo_tag_sid = $obj->photo_tag_sid;
            unset($obj->photo_tag_sid);
			if(empty($photo_mapping[$photo_id])){
                $obj->photo_tags_sid = array();
                $photo_mapping[$photo_id] = $obj;
            }
            $photo_mapping[$photo_id]->photo_tags_sid[] = $photo_tag_sid;
		}

        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_PHOTO_JOB,
            //'data' => PhotoResource::collection(array_values ($photo_mapping)),
            'data' => array_values ($photo_mapping)
        ],Response::HTTP_OK);
    }

    /**
    * @SWG\Get(
    *         path="/api/mobile/job/plan/{sid}",
    *         tags={"MOBILE/JOB"},
    *         summary="Get data of Plan",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this Job.",
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
    /**
    * @SWG\Get(
    *         path="/api/job/plan/{sid}",
    *         tags={"ADMIN/JOB"},
    *         summary="Get data of Plan",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this Job.",
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
    public function plan_data($sid){
        $json = null;
        $json_content = null;
        $json_data = null;
        

        // Step 1 - Get plan
        $plan = Plan::whereSid($sid)->first();
        if(!empty($plan)){
            // Step 2 - Get plan_actual
            $plan_actual = Plan_actual::wherePlanSid($sid)->first();
            // var_dump($plan_actual);die();
            if(!empty($plan_actual)){
                // Step 3 - Get plan_data
                $plan_actual_sid = $plan_actual->sid;
                if(!empty($plan_actual_sid)){
                    // Step 2 - Get plan_data
                   $plan_data = Plan_data::wherePlanActualSid($plan_actual_sid)->first();
                   if(!empty($plan_data)){
                        $json_content = $plan_data->data;
                   }
                }
            } else {
                // Step 4 - Get json_structure
                // $json_structure = JsonStructure::latest()->first();
                // if(!empty($json_structure)){
                //     $json_content = $json_structure->content;
                // }
                $json_content = null;
            }
        }
        
        if(!empty($json_content)){
            $json_data = json_decode($json_content, true);
        }
        $store_sid = $plan->store_sid;
        $store = Store::whereSid($store_sid)->first();
        
        $data_return = array(
            'plan_sid' => $sid,
            'plan' => $plan,
            'data' => $json_data,
            'store' => $store,
        );

        return response()->json($data_return,Response::HTTP_OK);
    }

    /**
    * @SWG\Post(
    *         path="/api/mobile/job/plan/{sid}",
    *         tags={"MOBILE/JOB"},
    *         summary="Upload test data of jobstore(plan) by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this plan.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *                   @SWG\Property(property="data", type="string", example="{}"),
    *               ),
    *               
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
    /**
    * @SWG\Post(
    *         path="/api/job/plan/{sid}",
    *         tags={"ADMIN/JOB"},
    *         summary="Upload test data of jobstore(plan) by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this plan.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *                   @SWG\Property(property="data", type="string", example="{}"),
    *               ),
    *               
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
    public function plan_data_upload(Request $request,$sid)
    {
        $return_data = null;

        DB::beginTransaction();
        try {
            $data = $request->data;
            $data_json_text = '';
            $job_status_old = '';
            $job = null;
            if(!empty($data)){
                // TODO: VALIDATE JSON
                try
                {
                    $data_json_text = json_encode($data);
                }
                catch (\Exception $e)
                {
                    throw new \Exception("Encode json error. Message: ".$e->getMessage());
                }
                
                if(!empty($data_json_text)){
                    // Get plan
                    $plan = Plan::whereSid($sid)->first();
                    if(!empty($plan)){
                        if($plan->status == 'DONE'){
                            $return_data = response()->json(
                                Exception::storeJobDone(),403);
                        } else {
                            $job = Job::whereSid($plan->job_sid)->first();
                            if(!empty($job)){
                                $job_status_old = $job->status;
                            } else {
                                $return_data = response()->json(
                                    Exception::notFoundJob(),404);
                            }

                        }
                    } else {
                        $return_data = response()->json(
                            Exception::notFoundData(),404);
                    }
                } else {
                    $return_data = response()->json(
                        Exception::dataInvalid(),400);
                }
            } else {
                $return_data = response()->json(
                    Exception::dataEmpty(),400);
            }
            // var_dump($data);die();
        
            if(empty($return_data) && !empty($plan) && !empty($job)){
                $plan_actual = Plan_actual::wherePlanSid($sid)->first();
                // var_dump($plan_actual);die();
                if(!empty($plan_actual)){
                    // TODO
                    $plan_actual_sid = $plan_actual->sid;
                } else {
                    // Create plan_actual
                    $plan_actual_sid = Uuid::generate()->string;
                    DB::table('plan_actual')->insert([
                        'sid' => $plan_actual_sid,
                        'plan_sid' => $sid,
                        'actual_date' => date('Y-m-d'),
                        'is_manual' => 0,
                        'status' => 'NEW', // TODO
                    ]);
                }
                if(!empty($plan_actual_sid)){
                    // Step 2 - Get plan_data
                    $plan_data = Plan_data::wherePlanActualSid($plan_actual_sid)->first();
                    if(!empty($plan_data)){
                        // Update plan_data
                        // $plan_data_sid = $plan_data->sid;
                        // TODO
                        $updated_data = array(
                            'data' => $data_json_text,
                        );
                        $plan_data->update($updated_data);
                    } else {
                        // Create plan_data
                        $plan_data_sid = Uuid::generate()->string;
                        DB::table('plan_data')->insert([
                            'sid' => $plan_data_sid,
                            'plan_actual_sid' => $plan_actual_sid,
                            'pg_id' => $plan->employee_sid,
                            'data' => $data_json_text,
                        ]);
                    }

                    // Update status of Plan
                    $plan_update = array(
                        'status' => 'DONE',
                    );
                    $status_update =$plan->update($plan_update);
                    if(!empty($status_update)){
                        $activiti = array();
                        $auth = \Auth::user();
                        $query_plan = "
                        SELECT stores.name AS store_name, jobs.name AS job_name
                        FROM 
                            plans 
                            INNER JOIN stores on plans.store_sid = stores.sid 
                            INNER JOIN jobs on plans.job_sid = jobs.sid 
                        WHERE plans.job_sid = '{$plan->job_sid}' AND plans.store_sid = '{$plan->store_sid}' AND plans.company_sid = '{$plan->company_sid}' AND plans.territory_sid = '{$plan->territory_sid}' AND plans.employee_sid = '{$auth->sid}'
                        ";
                        $obj_query_plan = DB::select($query_plan);
                        if(!empty($obj_query_plan)){
                            $obj_query_plan_first = $obj_query_plan[0];
                            $activiti['description'] = 'Job '.$obj_query_plan_first->job_name.' Store '.$obj_query_plan_first->store_name.' Synced to the server successfully' ;
                            $activiti['subject_type'] = 'App\Job\Plan';
                            $activiti['causer_sid'] = $auth->sid;
                            $activiti['causer_id'] = $auth->id;
                            $activiti['log_name'] = $auth->username;
                            Activity::create($activiti);
                            $log_arr = array(
                                'location' => __FILE__,
                                'function' => 'register_fcm_token',
                                '_POST' => !empty($plan_data) ? $plan_data : '',
                            );
                            debug_log_from_config($log_arr);
                        } else {
                            throw new \Exception("Not found related stores. Please check again.");
                        }
                    }

                    // Update status of Job
                    $store_completed_count = 0;
                    $query = "
                        SELECT COUNT(*) AS count_store
                        FROM 
                            plans 
                            INNER JOIN stores on plans.store_sid = stores.sid 
                        WHERE plans.job_sid = '{$plan->job_sid}' AND plans.status = 'DONE'
                    ";
                    // var_dump($query); die();
            
                    $obj_query = DB::select($query);
                    if(!empty($obj_query)){
                        $store_completed_count = $obj_query[0]->count_store;
                    }

                    $job_update = array(
                        'store_completed_count' => $store_completed_count,
                    );
                    if($job_status_old =='NEW'){
                        $job_update['status'] = 'INPROGRESS';
                    }
                    $job->update($job_update);
                    $data = [
                        'status' => 'Success',
                        'message' => Exception::UPLOAD_TEST_DATA_SUCCESS,
                        // 'job_sid' => $plan->job_sid,
                        // 'job_status_old' => $job_status_old,
                    ];
                    if($job_status_old=='INACTIVE'){
                        $data['message'] .= '. Job is in active';
                        $data['warning_code'] = 'job_inactive';
                    }
                    $return_data = response() ->json($data,Response::HTTP_OK);

                    DB::commit();
                }
            } else {
                // Do nothing
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            $message_db = $e->getMessage();
            $return_data = response()->json([
                'status' => 'Failed',
                // 'message' => 'DB error',
                'message' => $message_db,
            ],404);
        }

        return $return_data;
    }
      /**
    * @SWG\Get(
    *         path="/api/job/photos",
    *         tags={"ADMIN/JOB"},
    *         summary="Get list Photo of Jobs",
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
    *         @SWG\Parameter(
    *             name="job_sid",
    *             description="A UUID string identifying this company.",
    *             in="query",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="territory_sid",
    *             description="A UUID string identifying this territory.",
    *             in="query",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *             name="company_sid",
    *             description="A UUID string identifying this company.",
    *             in="query",
    *             required=true,
    *             type="string"
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
    public function jobs_list_photos(Request $request){
        // $query = "
        //         SELECT 
        //         plans.sid AS plan_sid,
        //         photo.id, photo.sid,photo.plan_actual_sid,photo.img_photo, photo.elevation_code, photo.name, photo.description,photo.created_by,photo.updated_by,photo.created_at,photo.updated_at
        //         FROM 
        //             plans 
        //             INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid
        //             INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid
        //         WHERE plans.status NOT IN ('INACTIVE')
        //             AND plans.job_sid = '{$request->job_sid}'
        //             AND plans.territory_sid = '{$request->territory_sid}'
        //             AND plans.company_sid = '{$request->company_sid}'
        //     ";
        //     $obj_list = DB::select($query);
        //     return PhotoResources::collection($obj_list);
            $page_size = $request->page_size;
            if(empty($page_size)){
                $page_size = 15;
            }
                $photo = DB::table('photo')
                    ->join('plan_actual', 'photo.plan_actual_sid', '=', 'plan_actual.sid')
                    ->join('plans', 'plan_actual.plan_sid', '=', 'plans.sid')
                    ->join('companies', 'plans.company_sid', '=', 'companies.sid')
                    ->join('employees', 'plans.employee_sid', '=', 'employees.sid')
                    ->join('territories', 'plans.territory_sid', '=', 'territories.sid')
                    ->join('jobs', 'plans.job_sid', '=', 'jobs.sid')
                    ->join('stores', 'plans.store_sid', '=', 'stores.sid')
                    ->join('photo_elevations', 'photo.elevation_code', '=', 'photo_elevations.code')
                    ->select('photo.id', 'photo.sid','photo.plan_actual_sid','photo.img_photo', 'photo.elevation_code', 'photo.name', 'photo.description', 'photo.created_by', 'photo.updated_by', 'photo.created_at', 'photo.updated_at', 'companies.sid AS companies_sid', 'companies.name AS companies_name', 'employees.sid AS employees_sid', 'employees.username AS employees_name', 'territories.sid AS territories_sid', 'territories.name AS territories_name', 'jobs.sid AS jobs_sid', 'jobs.name AS jobs_name', 'stores.sid AS stores_sid', 'stores.name AS stores_name','photo_elevations.sid AS photo_elevations_sid', 'photo_elevations.code AS photo_elevations_code', 'photo_elevations.name AS photo_elevations_name')
                    ->where('plans.job_sid', $request->job_sid )
                    ->where('plans.territory_sid', $request->territory_sid )
                    ->where('plans.company_sid', $request->company_sid )
                    ->orderBy('id','desc')->paginate($page_size);
                //return $photo;
                // $stores_list = DB::select($query);
                return ListPhotoResources::collection($photo);
        
    }
      /**
    * @SWG\Get(
    *         path="/api/job/store/photos",
    *         tags={"ADMIN/JOB"},
    *         summary="Get list Photo of Jobs",
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
    *         @SWG\Parameter(
    *             name="job_sid",
    *             description="A UUID string identifying this job.",
    *             in="query",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="store_sid",
    *             description="A UUID string identifying this stores.",
    *             in="query",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="territory_sid",
    *             description="A UUID string identifying this territory.",
    *             in="query",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *             name="company_sid",
    *             description="A UUID string identifying this company.",
    *             in="query",
    *             required=true,
    *             type="string"
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
    public function jobs_store_list_photos(Request $request){
        // $query = "
        //         SELECT 
        //         plans.sid AS plan_sid,
        //         photo.id, photo.sid,photo.plan_actual_sid,photo.img_photo, photo.elevation_code, photo.name, photo.description,photo.created_by,photo.updated_by,photo.created_at,photo.updated_at
        //         FROM 
        //             plans 
        //             INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid
        //             INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid
        //         WHERE plans.status NOT IN ('INACTIVE')
        //             AND plans.job_sid = '{$request->job_sid}'
        //             AND plans.territory_sid = '{$request->territory_sid}'
        //             AND plans.company_sid = '{$request->company_sid}'
        //             AND plans.store_sid = '{$request->store_sid}'
        //     ";
        //     $obj_list = DB::select($query);
        //     return PhotoResources::collection($obj_list);
            $page_size = $request->page_size;
            if(empty($page_size)){
                $page_size = 15;
            }
                $photo = DB::table('photo')
                    ->join('plan_actual', 'photo.plan_actual_sid', '=', 'plan_actual.sid')
                    ->join('plans', 'plan_actual.plan_sid', '=', 'plans.sid')
                    ->join('companies', 'plans.company_sid', '=', 'companies.sid')
                    ->join('employees', 'plans.employee_sid', '=', 'employees.sid')
                    ->join('territories', 'plans.territory_sid', '=', 'territories.sid')
                    ->join('jobs', 'plans.job_sid', '=', 'jobs.sid')
                    ->join('stores', 'plans.store_sid', '=', 'stores.sid')
                    ->join('photo_elevations', 'photo.elevation_code', '=', 'photo_elevations.code')
                    ->select('photo.id', 'photo.sid','photo.plan_actual_sid','photo.img_photo', 'photo.elevation_code', 'photo.name', 'photo.description', 'photo.created_by', 'photo.updated_by', 'photo.created_at', 'photo.updated_at', 'companies.sid AS companies_sid', 'companies.name AS companies_name', 'employees.sid AS employees_sid', 'employees.username AS employees_name', 'territories.sid AS territories_sid', 'territories.name AS territories_name', 'jobs.sid AS jobs_sid', 'jobs.name AS jobs_name', 'stores.sid AS stores_sid', 'stores.name AS stores_name','photo_elevations.sid AS photo_elevations_sid', 'photo_elevations.code AS photo_elevations_code', 'photo_elevations.name AS photo_elevations_name')
                    ->where('plans.job_sid', $request->job_sid )
                    ->where('plans.store_sid', $request->store_sid )
                    ->where('plans.territory_sid', $request->territory_sid )
                    ->where('plans.company_sid', $request->company_sid )
                    ->orderBy('id','desc')->paginate($page_size);
                //return $photo;
                // $stores_list = DB::select($query);
                return ListPhotoResources::collection($photo);
    }
}