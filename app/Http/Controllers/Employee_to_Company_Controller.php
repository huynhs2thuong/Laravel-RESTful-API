<?php

namespace App\Http\Controllers;

use App\Company;
use App\CustomerToCompany;
use App\Employee;
use App\Employee_to_company;
use App\Exceptions\Exception;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\JobEmployeeResource;
use App\Http\Resources\JobREsource;
use App\Http\Resources\ListPhotoResources;
use App\Http\Resources\StoreResource;
use App\Http\Resources\TerritorytoCompanyResources;
use App\Job;
use App\Job_to_Territory;
use App\StoreType;
use App\Territory;
use DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class Employee_to_Company_Controller extends Controller
{
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
    * @SWG\Get(
    *         path="/api/list_companies",
    *         tags={"ADMIN/EMPLOYEE/COMPANY"},
    *         summary="Get list companies",
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
    public function list_company(Request $request){
        $company = Company::orderBy('id','desc')->get();
        return CompanyResource::collection($company);
    }
    /**
    * @SWG\Get(
    *         path="/api/employees/{sid}/companies",
    *         tags={"ADMIN/EMPLOYEE/COMPANY"},
    *         summary="Lish Employee to Companies by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this Employee.",
    *             in="path",
    *             required=true,
    *             type="string",
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
    public function show($sid)
    {

        // TODO - Need to improve more performance
        $employee_to_company = Employee_to_company::whereEmployeeSid($sid)->get(['id','employee_sid','company_sid','territory_sid','is_active']);
        $data = [];

        foreach($employee_to_company as $key => $item){
            $territory = Territory::where('sid',$item->territory_sid)->select(['sid', 'code','name'])->first();
            $company = Company::where('sid',$item->company_sid)->select(['sid', 'name','email','phone','address_1','address_2','city','state','zip_code'])->first();
            if($item->is_active == 0){
                $active = false;
            }else{
                $active = true;
            }
            $employee_to_company[$key]['is_active'] = $active;
            $employee_to_company[$key]['company'] = $company;
            $employee_to_company[$key]['territory'] = $territory;
        }
        return response() ->json([
            'status' => 'Success',
            'message' => Exception::SHOW_EMPLOYEE_TO_COMPANY,
            'data'=>$employee_to_company
        ],Response::HTTP_OK);
        // return response() ->json([
        //     'data'=>$employee_to_company
        // ]);
    }

    /**
    * @SWG\Post(
    *         path="/api/employees/{sid}/companies",
    *         tags={"ADMIN/EMPLOYEE/COMPANY"},
    *         summary="Create Employee to Companies by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this Employee.",
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
    *                   required={"company_sid","territory_sid"},
    *                   @SWG\Property(property="company_sid", type="string", example="string"),
    *                   @SWG\Property(property="territory_sid", type="string", example="string"),
    *                   @SWG\Property(property="is_active", type="boolean", example=true),
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
    public function store(Request $request,$sid)
    {
        // TODO - Validate
        $data = $request->all(); // TODO - Validate
		$data['employee_sid'] = $sid;
		$input_data = $data;
        $params['input_data'] = $input_data;
        $params['action'] = 'create';

        $return_data = $this->data_check($params); // Alway check this data
        if(empty($return_data)){
            $item = Employee_to_company::create($data);
            if(!empty($item)){
                $employee_to_company = Employee_to_company::whereEmployeeSid($sid)->get(['id','employee_sid','company_sid','territory_sid','is_active']);
                $data = [];
        
                foreach($employee_to_company as $key => $item){
                    $territory = Territory::where('sid',$item->territory_sid)->select(['sid', 'code','name'])->first();
                    $company = Company::where('sid',$item->company_sid)->select(['sid', 'name','email','phone','address_1','address_2','city','state','zip_code'])->first();
                    if($item->is_active == 1){
                        $active = true;
                    }else{
                        $active = false;
                    }
                    $employee_to_company[$key]['is_active'] = $active;
                    $employee_to_company[$key]['company'] = $company;
                    $employee_to_company[$key]['territory'] = $territory;
                }
                $return_data = response() ->json([
                    'status' => 'Success',
                    'message' => Exception::LIST_COMPANY_OF_EMPLOYEE,
                    'data'=>$employee_to_company
                ],Response::HTTP_OK);
            }
        }
        
        return $return_data;
    }
    /**
    * @SWG\Put(
    *         path="/api/employees/{employee_sid}/companies/{id}",
    *         tags={"ADMIN/EMPLOYEE/COMPANY"},
    *         summary="Update Employee to Companies by UUID",
    *         @SWG\Parameter(
    *             name="employee_sid",
    *             description="A UUID string identifying this Employee.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *             name="id",
    *             description="A id string identifying this Territory to Company.",
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
    *                   required={"company_sid","territory_sid"},
    *                   @SWG\Property(property="company_sid", type="string", example="string"),
    *                   @SWG\Property(property="territory_sid", type="string", example="string"),
    *                   @SWG\Property(property="is_active", type="string", example=true),
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
    public function update(Request $request, $employee_sid,$id)
    {
        // TODO - Validate
        $id = (int)$id;
        $data = $request->all(); // TODO - Validate
        $input_data = $data;
        $territory_sid = $request->territory_sid;
        $company_sid = $request->company_sid;
        $is_active = (bool)$request->is_active;
        $input_data['id'] = $id;
        $input_data['employee_sid'] = $employee_sid;
        $input_data['is_active'] = $is_active;
        $params['input_data'] = $input_data;
        $params['action'] = 'update';
        // echo '$id = '.$id;die();
        
        if(!empty($id)){
            // Check change
            $check_obj = Employee_to_company::where('id',$id)->first();
        } else {
            $return_data = response()->json(
                Exception::invalid_id(),400);
        } 
        if(!empty($check_obj)){
            $flag_change = !($check_obj->company_sid == $company_sid && $check_obj->territory_sid == $territory_sid);
            $input_data['flag_change'] = $flag_change;
            if($is_active){
                // if($flag_change){
                    $return_data = $this->data_check($params); // Alway check this data
                //}
                // Not auto assign job because there is only 1 job assigned to 1 employee of 1 territory of 1 company
            } else { // Inactive
                if($flag_change){
                    // Allow
                } else { // Inactive current record
                    // Update job inactive, plan inactive
                    $condition = "company_sid = '{$company_sid}' AND territory_sid = '{$territory_sid}' AND employee_sid = '{$employee_sid}'";
                    $sql = "
                        SELECT count(*) AS count_total
                        FROM plans WHERE {$condition}
                    ";
                    $query_list = DB::select($sql);
                    $count_store = 0;
                    foreach($query_list as $query_obj){
                        $count_store = $query_obj->count_total;
                    }
                    if($count_store > 0){
                        $sql = "
                        UPDATE plans SET status = 'INACTIVE'
                        WHERE {$condition}
                        ";
                        DB::update($sql);
                        $message_store = "{$count_store} stores were inactive.";

                        $condition = "job_to_territories.company_sid = '{$company_sid}' AND job_to_territories.territory_sid = '{$territory_sid}'";
                        $sql = "
                            SELECT count(*) AS count_total
                            FROM  job_to_territories WHERE {$condition}
                        ";
                        $query_list = DB::select($sql);
                        $count_job = 0;
                        foreach($query_list as $query_obj){
                            $count_job = $query_obj->count_total;
                        }
                        if($count_job > 0){
                            $sql = "
                            UPDATE job_to_territories SET status = 0
                            WHERE {$condition}
                            ";
                            DB::update($sql);

                            $sql = "
                            UPDATE jobs INNER JOIN job_to_territories ON jobs.sid = job_to_territories.job_sid
                            SET jobs.status = 'INACTIVE'
                            WHERE {$condition}
                            ";
                            DB::update($sql);

                            $message_job = "{$count_job} jobs were inactive.";
                        }
                    }
                }
            }
            if(empty($return_data)){
                $data = $request->all(); // TODO - Validate
                // var_dump($data);die();
                $item = Employee_to_company::whereId($id)->first();
                $item->update($data);
                if(!empty($item)){
                    $employee_to_company = Employee_to_company::whereId($id)->first();
                    $territory = Territory::where('sid',$employee_to_company->territory_sid)->select(['sid', 'code','name'])->first();
                    $company = Company::where('sid',$item->company_sid)->select(['sid', 'name','email','phone','address_1','address_2','city','state','zip_code'])->first();
                    if($item->is_active == 0){
                        $active = false;
                    }else{
                        $active = true;
                    }
                    $employee_to_company['is_active'] = $active;
                    $employee_to_company['company'] = $company;
                    $employee_to_company['territory'] = $territory;

                    if($active){
                        $return_data = $this->data_check($params);
                    }
                    else 
                    {
                        
                    }
                    $message = 'Edit information successfully.';
                    if(!empty($message_job)){
                        $message .= ' '.$message_job;
                    }
                    if(!empty($message_store)){
                        $message .= ' '.$message_store;
                    }
                    $return_data = response()->json([
                        'status' => 'Success',
                        'message' => $message,
                        'data' => $employee_to_company
                    ],Response::HTTP_OK);
                    // return response($employee_to_company, Response::HTTP_CREATED);
                }
            }
        } else {
            $return_data = response()->json(
                Exception::employeeNotFound(),400);
        }

        return $return_data;
    }
    private function data_check($params){ // Assume $flag_change = true
        $return_data = null;

        $action = $params['action'];
        $input_data = $params['input_data'];
        // $flag_change = $params['flag_change'];
		// echo json_encode($input_data);die();
        if($action == 'create' || $action == 'update'){ // Applied for these actions
            $company_sid = $input_data['company_sid'];
            $territory_sid = $input_data['territory_sid'];
            $employee_sid = $input_data['employee_sid'];

            // Check only one unique key
            $check_mapping = array(
                'territory_sid' => $territory_sid,
                // 'employee_sid' => $employee_sid,
            );
            foreach($check_mapping as $key => $value){
                $condition = "{$key} = '{$value}'";
                if(!empty($input_data['id'])){
                    $id = $input_data['id'];
                    $condition .= " AND id <> {$id}"; 
                }
                
                $sql = "
                    SELECT count(*) AS count_total
                    FROM employee_to_companies 
                    WHERE {$condition} AND is_active = 1
                "; // Asume company_sid, territory_sid, employee_sid, is_active is unique
                // die($sql);
                $query_list = DB::select($sql);
                $count_total = 0;
                foreach($query_list as $query_obj){
                    $count_total = $query_obj->count_total;
                }
                if($count_total > 0){
                    $return_data = response()->json(
                        Exception::checkEmployee(),400);
                    break; // End loop
                }
            }
			
			if($action == 'create'){
				// Find employee_sid_old
				$condition = "company_sid = '{$company_sid}' AND territory_sid = '{$territory_sid}'";
				$sql = "
                    SELECT employee_sid
                    FROM employee_to_companies 
                    WHERE {$condition}
					LIMIT 1
                "; // Asume company_sid, territory_sid, employee_sid, is_active is unique
                // die($sql);
                $query_list = DB::select($sql);
                $count_total = 0;
                foreach($query_list as $query_obj){
                    $employee_sid_old = $query_obj->employee_sid;
					break;
                }
				if(!empty($employee_sid_old)){
                    $condition .= " AND employee_sid = '{$employee_sid_old}' AND plans.status = 'NEW'";
					$sql = "UPDATE plans SET employee_sid = '{$employee_sid}' WHERE {$condition}";
					// die($sql);
					DB::update($sql);
				}
                
			}
			if($action == 'update'){
				if(!empty($input_data['flag_change'])){ // Chuyển vùng cho nhân viên
					// TODO
				}
			}
        } else if($action == 'delete'){
            $company_sid = $input_data['company_sid'];
            $territory_sid = $input_data['territory_sid'];
            $employee_sid = $input_data['employee_sid'];
            // Step 1: Check related jobs
            $condition = "company_sid = '{$company_sid}' AND territory_sid = '{$territory_sid}' AND employee_sid = '{$employee_sid}'";
            $sql = "
                SELECT count(*) AS count_total
                FROM plans WHERE {$condition}
            ";
            $query_list = DB::select($sql);
            $count_total = 0;
            foreach($query_list as $query_obj){
                $count_total = $query_obj->count_total;
            }
            if($count_total > 0){
                $return_data = response()->json(
                    Exception::unassignJobBefore(),400);
            }

            // Step 2: Check related plans => No need
        }

        return $return_data;
    }


    /**
    * @SWG\Delete(
    *         path="/api/employees_to_company/{id}",
    *         tags={"ADMIN/EMPLOYEE/COMPANY"},
    *         summary="Delete Employee to Companies by UUID",
    *         @SWG\Parameter(
    *             name="id",
    *             description="A id identifying this Employee to Company.",
    *             in="path",
    *             required=true,
    *             type="string",
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
    public function destroy($id)
    {
        $obj = Employee_to_company::whereId($id)->first();
        // echo json_encode($obj);die();
        if(!empty($obj)){
            $input_data = array();
            $input_data['company_sid'] = $obj->company_sid;
            $input_data['territory_sid'] = $obj->territory_sid;
            $input_data['employee_sid'] = $obj->employee_sid;
            $input_data['id'] = $id;
            $params['input_data'] = $input_data;
            $params['action'] = 'delete';
            $return_data = $this->data_check($params); // Alway check this data
    
            if(empty($return_data)){
                $obj->delete();
                
                $return_data = response()->json([
                    'status' => 'Success',
                    'message' => Exception::DELETE_SUCCESS,
                ],Response::HTTP_OK);
            }
        }
        else {
            $return_data = response()->json(
                Exception::deleteFailed(),400);
        }

        return $return_data;

    }
    /**
    * @SWG\Get(
    *         path="/api/mobile/employee/jobs",
    *         tags={"MOBILE/EMPLOYEE"},
    *         summary="Get list Job of employees",
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
    public function employee_list_jobs_mobile() {
        $auth = \Auth::user();
        $obj_list = DB::select("
            SELECT jobs.id, jobs.sid, jobs.code, jobs.name, jobs.status, jobs.company_sid, jobs.created_at, jobs.updated_at , plans.territory_sid,
                jobs.store_count, jobs.store_completed_count, jobs.store_in_process_count,
                plans.sid AS plan_sid, plans.status AS plan_status
            FROM 
                plans 
                INNER JOIN jobs on plans.job_sid = jobs.sid 
            WHERE jobs.status NOT IN ('INACTIVE') AND plans.status NOT IN ('INACTIVE') AND plans.employee_sid = '{$auth->sid}'
        ");
        $job_mapping = array();
        foreach($obj_list as $obj){
            $job_id = $obj->id;
            $plan_status = $obj->plan_status;
            $plan_sid = $obj->plan_sid;
            if(empty($job_mapping[$job_id])){
                $obj->in_progress_mapping = array();
                $job_mapping[$job_id] = $obj;
            }
            $obj_job = &$job_mapping[$job_id];
            if($plan_status == 'INPROGRESS'){
                $obj_job->in_progress_mapping[] = $plan_sid;
            }

            unset($obj_job);
        }
        // var_dump($job_mapping);die();
		
		return response()->json([
			'status' => "Success",
			'message' => Exception::LIST_JOB_EMPLOYEE,
			'data' => JobEmployeeResource::collection(array_values ($job_mapping))
		],Response::HTTP_OK);
    }
    /**
    * @SWG\Get(
    *         path="/api/employee/{sid}/job",
    *         tags={"ADMIN/EMPLOYEE/COMPANY"},
    *         summary="Get list Job of employees",
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
    // public function employee_list_jobs($sid) {
    //     $job = DB::select('select jobs.id, jobs.sid, jobs.code, jobs.name, jobs.status, jobs.company_sid, jobs.created_at, jobs.updated_at , job_to_territories.territory_sid
    //         from jobs 
    //         inner join job_to_territories on job_to_territories.job_sid = jobs.sid 
    //         inner join employee_to_companies on job_to_territories.company_sid = employee_to_companies.company_sid 
    //         inner join companies on job_to_territories.company_sid = companies.sid 
    //         where job_to_territories.company_sid = employee_to_companies.company_sid 
    //         and job_to_territories.territory_sid = employee_to_companies.territory_sid 
    //         and employee_to_companies.employee_sid = "' .$sid.'"');
    //         return response()->json([
    //             'status' => "Success",
    //             'message' => 'List jobs of employees',
    //             'data' => JobEmployeeResource::collection($job)
    //         ],Response::HTTP_OK);
    // }
    public function employee_list_jobs($sid) {
        $obj_list = DB::select("
            SELECT jobs.id, jobs.sid, jobs.code, jobs.name, jobs.status, jobs.company_sid, jobs.created_at, jobs.updated_at , plans.territory_sid,
                jobs.store_count, jobs.store_completed_count, jobs.store_in_process_count
            FROM 
                plans 
                INNER JOIN jobs on plans.job_sid = jobs.sid 
            WHERE plans.employee_sid = '{$sid}'
        ");
        $job_mapping = array();
        foreach($obj_list as $obj){
            $job_id = $obj->id;
            if(empty($job_mapping[$job_id])){
                $job_mapping[$job_id] = $obj;
            }
        }
		
		return response()->json([
			'status' => "Success",
			'message' => Exception::LIST_JOB_EMPLOYEE,
			'data' => JobEmployeeResource::collection(array_values ($job_mapping))
		],Response::HTTP_OK);
    }

    /**
    * @SWG\Get(
    *         path="/api/mobile/employee/stores",
    *         tags={"MOBILE/EMPLOYEE"},
    *         summary="Get list Stores of employees",
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
    public function employee_list_stores_mobile() {
        $auth = \Auth::user();
		$obj_list = DB::select("
            SELECT 
            plans.sid AS plan_sid, plans.status,
            stores.id, stores.sid, stores.name ,stores.phone, stores.climate_region_sid, stores.store_type_sid, stores.address_1, stores.address_2, stores.city, stores.state, stores.zip_code, stores.img_store, stores.file_store, stores.a2_file_number, stores.a2_day_on_file, stores.opening_hour,stores.is_active, stores.company_sid, stores.territory_sid, stores.created_at,stores.updated_at 
            FROM 
                plans 
                INNER JOIN stores on plans.store_sid = stores.sid 
            WHERE plans.is_active = 1 AND stores.is_active = 1 AND
                plans.status NOT IN ('INACTIVE') AND plans.employee_sid = '{$auth->sid}'
        ");
        $store_mapping = array();
        foreach($obj_list as $obj){
            $store_id = $obj->id;
            if(empty($store_mapping[$store_id])){
                $store_mapping[$store_id] = $obj;
            }
        }

        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_STORE_EMPLOYEE,
            'data' => StoreResource::collection(array_values ($store_mapping))
        ],Response::HTTP_OK);

    }
    /**
    * @SWG\Get(
    *         path="/api/employee/{sid}/store",
    *         tags={"ADMIN/EMPLOYEE/COMPANY"},
    *         summary="Get list Stores of employees",
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
    // public function employee_list_stores($sid) {
        //$job = Job::join('job_to_territories','job_to_territories.job_sid', '=', 'jobs.sid')->join('employee_to_companies','job_to_territories.company_sid', '=', 'employee_to_companies.company_sid')->join('companies','job_to_territories.company_sid', '=', 'companies.sid')->where('job_to_territories.territory_sid','employee_to_companies.territory_sid')->where('employee_to_companies.employee_sid',$sid)->get(['jobs.sid','jobs.code','jobs.name','jobs.status','jobs.company_sid','companies.name AS company_name','job_to_territories.territory_sid']);
        /*
        $store = DB::select('select stores.id, stores.sid, stores.name ,stores.phone, stores.climate_region_sid, stores.store_type_sid, stores.address_1, stores.address_2, stores.city, stores.state, stores.zip_code, stores.img_store, stores.file_store, stores.a2_file_number, stores.a2_day_on_file, stores.opening_hour,stores.is_active,stores.company_sid, stores.territory_sid,stores.created_at,stores.updated_at
        from stores 
        inner join employee_to_companies on stores.company_sid = employee_to_companies.company_sid 
        inner join companies on stores.company_sid = companies.sid 
        where stores.company_sid = employee_to_companies.company_sid 
        and stores.territory_sid = employee_to_companies.territory_sid 
        and employee_to_companies.employee_sid = "' .$sid.'"');
        return response()->json([
            'status' => "Success",
            'message' => 'List stores of employees',
            'data' => StoreResource::collection($store)
        ],Response::HTTP_OK);
        */
        //return StoreResource::collection($store);
    // }

    public function employee_list_stores($sid) {
		$obj_list = DB::select("
            SELECT 
            plans.sid AS plan_sid, plans.status,
            stores.id, stores.sid, stores.name ,stores.phone, stores.climate_region_sid, stores.store_type_sid, stores.address_1, stores.address_2, stores.city, stores.state, stores.zip_code, stores.img_store, stores.file_store, stores.a2_file_number, stores.a2_day_on_file, stores.opening_hour,stores.is_active, stores.company_sid, stores.territory_sid, stores.created_at,stores.updated_at 
            FROM 
                plans 
                INNER JOIN stores on plans.store_sid = stores.sid 
            WHERE plans.employee_sid = '{$sid}'
        ");
        $store_mapping = array();
        foreach($obj_list as $obj){
            $store_id = $obj->id;
            if(empty($store_mapping[$store_id])){
                $store_mapping[$store_id] = $obj;
            }
        }

        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_STORE_EMPLOYEE,
            'data' => StoreResource::collection(array_values ($store_mapping))
        ],Response::HTTP_OK);

    }
    /**
    * @SWG\Get(
    *         path="/api/customer/company/photos",
    *         tags={"ADMIN/EMPLOYEE/COMPANY"},
    *         summary="Get list Photo of Company",
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
    *             name="search",
    *             description="Search keyword",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="company_sid",
    *             description="filter company",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="territory_sid",
    *             description="filter territory",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="employee_sid",
    *             description="filter employee",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="store_sid",
    *             description="filter store",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="job_sid",
    *             description="filter job",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="elevation_code",
    *             description="filter elevation",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="test_metrie",
    *             description="filter test_metrie",
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
    public function customer_company_list_photos(Request $request){
        $auth = \Auth::user();
        $page_size = $request->page_size;
        if(empty($page_size)){
            $page_size = 15;
        }
        $is_search_filter = false;
        $company_sid = $request->company_sid;
        $territory_sid = $request->territory_sid;
        $job_sid = $request->job_sid;
        $store_sid = $request->store_sid;
        $employee_sid = $request->employee_sid;
        $elevation_code = $request->elevation_code;
        $description = $request->search;
        $test_metrics = $request->test_metrics;
        
        $photo_list = DB::table('plans')
            ->join('plan_actual', 'plan_actual.plan_sid', '=', 'plans.sid')
            ->join('photo', 'photo.plan_actual_sid', '=', 'plan_actual.sid')
            ->join('companies', 'plans.company_sid', '=', 'companies.sid')
            ->join('employees', 'plans.employee_sid', '=', 'employees.sid')
            ->join('territories', 'plans.territory_sid', '=', 'territories.sid')
            ->join('jobs', 'plans.job_sid', '=', 'jobs.sid')
            ->join('stores', 'plans.store_sid', '=', 'stores.sid')
            ->join('photo_elevations', 'photo.elevation_code', '=', 'photo_elevations.code')
            ->select('photo.id', 'photo.sid','photo.plan_actual_sid','photo.img_photo', 'photo.elevation_code', 'photo.name', 'photo.description', 'photo.created_by', 'photo.updated_by', 'photo.created_at', 'photo.updated_at', 'companies.sid AS companies_sid', 'companies.name AS companies_name', 'employees.sid AS employees_sid', 'employees.username AS employees_name', 'territories.sid AS territories_sid', 'territories.name AS territories_name', 'jobs.sid AS jobs_sid', 'jobs.name AS jobs_name', 'stores.sid AS stores_sid', 'stores.name AS stores_name','photo_elevations.sid AS photo_elevations_sid', 'photo_elevations.code AS photo_elevations_code', 'photo_elevations.name AS photo_elevations_name');

        if (!empty($company_sid)) {
            $is_search_filter = true;
            $photo_list->where('plans.company_sid', $company_sid );
        }

        if (!empty($territory_sid)) {
            $is_search_filter = true;
            $photo_list->where('plans.territory_sid', $territory_sid );
        }

        if (!empty($store_sid)) {
            $is_search_filter = true;
            $photo_list->where('plans.store_sid', $store_sid );
        }

        if (!empty($job_sid)) {
            $is_search_filter = true;
            $photo_list->where('plans.job_sid', $job_sid );
        }
        if (!empty($employee_sid)) {
            $is_search_filter = true;
            $photo_list->where('plans.employee_sid', $employee_sid );
        }
        if (!empty($elevation_code)) {
            $is_search_filter = true;
            $photo_list->where('photo.elevation_code', $elevation_code );
        }
        if (!empty($description)) {
            $is_search_filter = true;
            $photo_list->where('photo.description', 'LIKE','%'.$description.'%' );
        }
        if (!empty($test_metrics)) {
            $is_search_filter = true;

            // var_dump($test_metrics);exit();
            $test_metrics_arr = explode(',', $test_metrics);
            // var_dump($test_metrics_arr);exit();
            $query_obj_list = DB::table('photo_job_tag')->whereIn('photo_tag_sid', $test_metrics_arr)->select('photo_sid')->get();
            $photo_sid_arr = array();
            if(!empty($query_obj_list)){
                foreach($query_obj_list as $query_obj){
                    $photo_sid_arr[] = $query_obj->photo_sid;
                }
                
            }
            // var_dump($photo_sid_arr);exit();
            if(!empty($photo_sid_arr)){
                $photo_list->whereIn('photo.sid', $photo_sid_arr); // TODO - Improve
            }

            // $photo_list->groupBy('photo.sid'); // Error
        }
        if ($is_search_filter === false) {
            $photo_list = DB::table('photo')
                ->join('plan_actual', 'photo.plan_actual_sid', '=', 'plan_actual.sid')
                ->join('plans', 'plan_actual.plan_sid', '=', 'plans.sid')
                ->join('companies', 'plans.company_sid', '=', 'companies.sid')
                ->join('employees', 'plans.employee_sid', '=', 'employees.sid')
                ->join('territories', 'plans.territory_sid', '=', 'territories.sid')
                ->join('jobs', 'plans.job_sid', '=', 'jobs.sid')
                ->join('stores', 'plans.store_sid', '=', 'stores.sid')
                ->join('photo_elevations', 'photo.elevation_code', '=', 'photo_elevations.code')
                ->select('photo.id', 'photo.sid','photo.plan_actual_sid','photo.img_photo', 'photo.elevation_code', 'photo.name', 'photo.description', 'photo.created_by', 'photo.updated_by', 'photo.created_at', 'photo.updated_at', 'companies.sid AS companies_sid', 'companies.name AS companies_name', 'employees.sid AS employees_sid', 'employees.username AS employees_name', 'territories.sid AS territories_sid', 'territories.name AS territories_name', 'jobs.sid AS jobs_sid', 'jobs.name AS jobs_name', 'stores.sid AS stores_sid', 'stores.name AS stores_name','photo_elevations.sid AS photo_elevations_sid', 'photo_elevations.code AS photo_elevations_code', 'photo_elevations.name AS photo_elevations_name');
            if ($auth->role == 'CUSTOMER') {
                $customer = CustomerToCompany::whereEmployeeSid($auth->sid)->first();
                $photo_list->where('plans.company_sid', $customer->company_sid);
                $result = $photo_list->orderBy('id','desc')->paginate($page_size);
                return ListPhotoResources::collection($result);
            }else{
                return response()->json(
                    Exception::checkCustomer()
                ,400);
            }    
        } 
        if ($is_search_filter === true) {
            $result = $photo_list->orderBy('id','desc')->paginate($page_size);
            return ListPhotoResources::collection($result);
        }
        

            // $obj_list = DB::select($query);
            // return ::collection($obj_list);
        
    }

    
}
