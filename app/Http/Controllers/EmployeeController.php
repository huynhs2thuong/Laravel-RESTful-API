<?php

namespace App\Http\Controllers;

use App\CustomerToCompany;
use App\Employee;
use App\Employee_to_company;
use App\Exceptions\Exception;
use App\FCM_token;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\EmloyeeAvatarResource;
use App\Http\Resources\EmployeeCustomerResources;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\FCMTokenResources;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Null_;
use Spatie\Activitylog\Models\Activity;
use Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Webpatser\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;

class EmployeeController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        return $this->user;

    }
    /**
    * @SWG\Get(
    *         path="/api/employees",
    *         tags={"ADMIN/EMPLOYEE"},
    *         summary="Get list Employee",
    *         @SWG\Parameter(
    *             name="search",
    *             description="Search employees by username, full name",
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
        $emloyees = Employee::orderBy('id','desc')->paginate($request->page_size);
        $page_size = $request->page_size;
        if(empty($page_size)){
            $page_size = 15;
        }
        $employees_list = DB::table('employees')
            ->select('employees.*');

        $is_search_filter = false;
        $search = $request->search;
        
        if(!empty($search)){
            $is_search_filter = true;
            $employees_list->where('employees.username','LIKE', '%'.$search.'%')
                            ->orwhere('employees.full_name','LIKE', '%'.$search.'%');
                    
        }
        if ($is_search_filter === false) {
            $result = Employee::orderBy('id','desc')->paginate($page_size);
        }
        $result = $employees_list->orderBy('id','desc')->paginate($page_size);
        return EmployeeResource::collection($result);
        
    }
    /**
    * @SWG\Post(
    *         path="/api/employees",
    *         tags={"ADMIN/EMPLOYEE"},
    *         summary="Create a Employee",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *                   required={"first_name","last_name","email","phone_number","role"},
    *                   @SWG\Property(property="first_name", type="string", example="string"),
    *                   @SWG\Property(property="last_name", type="string", example="string"),
    *                   @SWG\Property(property="username", type="string", example="string"),
    *                   @SWG\Property(property="password", type="string", example="string"),
    *                   @SWG\Property(property="phone_number", type="string", example="string"),
    *                   @SWG\Property(property="email", type="string", example="string"),
    *                   @SWG\Property(property="date_birth", type="string", example="YYYY-MM-DD"),
    *                   @SWG\Property(property="title", type="string", example="string"),
    *                   @SWG\Property(property="address_1", type="string", example="string"),
    *                   @SWG\Property(property="address_2", type="string", example="string"),
    *                   @SWG\Property(property="city", type="string", example="string"),
    *                   @SWG\Property(property="state", type="string", example="string"),
    *                   @SWG\Property(property="zip_code", type="string", example="string"),
    *                   @SWG\Property(property="role", type="string",enum={"ADMIN","EMPLOYEE", "CUSTOMER"}, example="string"),
    *                   @SWG\Property(property="company_sid", type="string", example="string"),
    *                   @SWG\Property(property="is_active", type="string", example=true)
    *               ),
    *               
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
    public function store(EmployeeRequest $request)
    {
        $auth = \Auth::user();
        $username = Employee::whereUsername($request->username)->first();
        if(!empty($username)){
            return response()->json(
                Exception::usernameDuplicated(),400);
        }
        $email = Employee::whereEmail($request->email)->first();
        if(!empty($email)){
            return response()->json(
                Exception::emailDuplicated(),400);
        }


        $sid = Uuid::generate()->string;
        $data = $request->all();
        // TODO - Fix undefined
        $data_temp = array();
        foreach($data as $data_key => $data_value){
            if($data_value != 'undefined'){
                $data_temp[$data_key] = $data_value;
            }
        }
        $data = $data_temp;
        
        // TODO - Validate dob
        // if(empty($data['date_birth'])){
        //     return response()->json([
        //         'status' => 'Failed',
        //         'message' => 'Date of birth is required.',
        //         'error_code' => "date_birth_required"
        //     ],400);
        // }
        
        $data['sid'] = $sid;
        $data['password'] = Hash::make($request->input('password'));
        $data['first_login'] = 0;
        $data['username'] = strtolower($request->username);
        $data['email'] = strtolower($request->email);
        $data['created_by'] = $auth->username;
        $data['role'] = $request->role;
        if($request->role == 'CUSTOMER'){
            $customer_data['employee_sid'] = $sid;
            $customer_data['company_sid'] = $request->company_sid;
            CustomerToCompany::create($customer_data);
        }
        if($request->is_active == 'true'){
            $data['is_active'] = 1;
        }else{
            $data['is_active'] = 0;
        }
        $data['full_name'] = $request->first_name. ' '. $request->last_name;

        $uuid_img = Uuid::generate()->string;
        $file = $request->file('avatar');
        if(!empty($file)){
            $name = 'avatar_'.$uuid_img;
            $url = \Storage::putFileAs('avatar',$file,$name. '.'.$file->extension());
            $data['avatar'] = env('APP_URL').'image/'.$url;
        }else{
            $data['avatar'] = Null;
        }
        
        $employee = Employee::create($data);
        if($request->role == 'CUSTOMER'){
                $employee['company_sid'] = $request->company_sid;
        }
        
        // $emloyee  = Employee::create(
        //     $request->only('first_name','last_name','username','email','phone_number','address','is_active') + ['first_login' => 0] +['role' => "FIELD"] + ['sid' => $sid] + ['password' => Hash::make($request->input('password'))]
        // );
        return response()->json([
            'status' => "Success",
            'message' => Exception::CREATED_EMPLOYEE_SUCCESS,
            'data' => new EmployeeCustomerResources($employee)
        ],Response::HTTP_CREATED);
        //return response(new EmployeeResource($emloyee), Response::HTTP_CREATED);
    }
    /**
    * @SWG\Get(
    *         path="/api/employees/{sid}",
    *         tags={"ADMIN/EMPLOYEE"},
    *         summary="Get a Employee by UUID",
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
    public function show($sid)
    {
        $employee = Employee::Where('sid',$sid)->first();
        if($employee->role == 'CUSTOMER'){
            $customer = CustomerToCompany::whereEmployeeSid($sid)->first();
            if(!empty($customer)){
                $employee['company_sid'] = $customer->company_sid;
            }
        }
        return response()->json([
            'status' => "Success",
            'message' => Exception::SHOW_EMPLOYEE,
            'data' => new EmployeeCustomerResources($employee)
        ],Response::HTTP_OK);
       // return new EmployeeResource($emloyee);
    }
    /**
    * @SWG\Put(
    *         path="/api/employees/{sid}",
    *         tags={"ADMIN/EMPLOYEE"},
    *         summary="Put a Employee by UUID",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *                   required={"first_name","last_name","username","phone_number","email"},
    *                   @SWG\Property(property="first_name", type="string", example="string"),
    *                   @SWG\Property(property="last_name", type="string", example="string"),
    *                   @SWG\Property(property="username", type="string", example="string"),
    *                   @SWG\Property(property="password", type="string", example="string"),
    *                   @SWG\Property(property="phone_number", type="string", example="string"),
    *                   @SWG\Property(property="email", type="string", example="string"),
    *                   @SWG\Property(property="date_birth", type="string", example="YYYY-MM-DD"),
    *                   @SWG\Property(property="title", type="string", example="string"),
    *                   @SWG\Property(property="address_1", type="string", example="string"),
    *                   @SWG\Property(property="address_2", type="string", example="string"),
    *                   @SWG\Property(property="city", type="string", example="string"),
    *                   @SWG\Property(property="state", type="string", example="string"),
    *                   @SWG\Property(property="zip_code", type="string", example="string"),
    *                   @SWG\Property(property="role", type="string",enum={"ADMIN","EMPLOYEE", "CUSTOMER"}, example="string"),
    *                   @SWG\Property(property="company_sid", type="string", example="string"),
    *                   @SWG\Property(property="is_active", type="string", example=true)
    *               ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this employee.",
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
    /**
    * @SWG\Patch(
    *         path="/api/employees/{sid}",
    *         tags={"ADMIN/EMPLOYEE"},
    *         summary="Put a Employee by UUID",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *                   required={"first_name","last_name","username","phone_number","email"},
    *                   @SWG\Property(property="first_name", type="string", example="string"),
    *                   @SWG\Property(property="last_name", type="string", example="string"),
    *                   @SWG\Property(property="username", type="string", example="string"),
    *                   @SWG\Property(property="password", type="string", example="string"),
    *                   @SWG\Property(property="phone_number", type="string", example="string"),
    *                   @SWG\Property(property="email", type="string", example="string"),
    *                   @SWG\Property(property="date_birth", type="string", example="YYYY-MM-DD"),
    *                   @SWG\Property(property="title", type="string", example="string"),
    *                   @SWG\Property(property="address_1", type="string", example="string"),
    *                   @SWG\Property(property="address_2", type="string", example="string"),
    *                   @SWG\Property(property="city", type="string", example="string"),
    *                   @SWG\Property(property="state", type="string", example="string"),
    *                   @SWG\Property(property="zip_code", type="string", example="string"),
    *                   @SWG\Property(property="role", type="string",enum={"ADMIN","EMPLOYEE", "CUSTOMER"}, example="string"),
    *                   @SWG\Property(property="company_sid", type="string", example="string"),
    *                   @SWG\Property(property="is_active", type="string", example=true)
    *               ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this employee.",
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
    public function update(Request $request, $sid)
    {
        $auth = \Auth::user();
        $employee = Employee::where('sid',$sid)->first();
        $username = Employee::where('username','LIKE',$request->username)->whereNotIn('username',[$employee->username])->first();
        if(!empty($username)){
            return response()->json(
                Exception::usernameDuplicated(),400);
        }
        $email = Employee::whereEmail($request->email)->whereNotIn('email',[$employee->email])->first();
        if(!empty($email)){
            return response()->json(
                Exception::emailDuplicated(),400);
        }
        $data = $request->all();

        // TODO - Not used
        //if($request->is_active == 'true' && $employee->is_active == 0){
            // Employee_to_company::whereEmployeeSid($employee->sid)->get()-> delete();
            //DB::delete('delete from employee_to_companies WHERE employee_to_companies.employee_sid = "' .$employee->sid.'"');
        ///}
        $data['username'] = strtolower($request->username);
        $data['email'] = strtolower($request->email);
        if($request->is_active == 'true'){
            $data['is_active'] = 1;
        }else{
            $data['is_active'] = 0;
        }
        if($request->role == 'CUSTOMER'){
            $customer = CustomerToCompany::where('employee_sid',$sid)->first();
            if(!empty($customer)){
                $customer_data['company_sid'] = $request->company_sid;
                // $customer_company = CustomerToCompany::where('company_sid',$request->company_sid)->whereNotIn('name',[$customer->company_sid])->first();
                // if(!empty($customer_company))
                $customer->update($customer_data);
                
            }
        }
        if(!empty($request->password)){
            $request->validate([
                'password' => 'required|min:8',
            ]);
            $employee->update($request->only('first_name','last_name','username','phone_number','email','date_birth','title','address_1','address_2','city','state','zip_code','role'));
            $data['full_name'] = $employee->first_name. ' '. $employee->last_name;
            $data['updated_by'] = $auth->username;
            $data['password'] = Hash::make($request->input('password'));
            $employee->update($data);
        }else {
            $employee->update($request->only('first_name','last_name','username','phone_number','email','date_birth','title','address_1','address_2','city','state','zip_code','role'));
            $data['full_name'] = $employee->first_name. ' '. $employee->last_name;
            $data['updated_by'] = $auth->username;
            $data['password'] = $employee->password;
            $employee->update($data);
        }
        
        $activiti = array();
        $auth = \Auth::user();
       
        $activiti['description'] = 'Update profile employee '.$employee->username;
        $activiti['subject_type'] = 'App\Employee';
        $activiti['causer_sid'] = $auth->sid;
        $activiti['causer_id'] = $auth->id;
        $activiti['log_name'] = $auth->username;
        Activity::create($activiti);
        //$employee->update($request->only('first_name','last_name','username','phone_number','email','date_birth','title','address_1','address_2','city','state','zip_code','role','is_active'));
        //$employee->update($data);
        $employee['company_sid'] = $request->company_sid;
        return response()->json([
            'status' => "Success",
            'message' => Exception::UPDATED_EMPLOYEE_SUCCESS,
            'data' => new EmployeeCustomerResources($employee)
        ],Response::HTTP_OK);
        //return response(new EmployeeResource($emloyee), Response::HTTP_ACCEPTED);
    }
    /**
    * @SWG\Post(
    *         path="/api/employees/{sid}/update-avatar",
    *         tags={"ADMIN/EMPLOYEE"},
    *         description="Update avatar employee",
    *         consumes={"multipart/form-data"},
    *         produces={"application/json"},
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this employee.",
    *             in="path",
    *             required=true,
    *             type="string",
    *         ),
    *         @SWG\Parameter(
    *             name="avatar",
    *             description="File name arvatar",
    *             in="formData",
    *             type="file",
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
    public function update_avatar(Request $request,$sid) {
        $employee = Employee::whereSid($sid)->first();
        $uuid_img = Uuid::generate()->string;
        $file = $request->file('avatar');
        if(!empty($file)) {
            $name = 'avata_'.$uuid_img;
            $url = \Storage::putFileAs('avatar',$file,$name. '.'.$file->extension());
            $employee->update(['avatar' => env('APP_URL').'image/'.$url]);
            
            $auth = \Auth::user();  
            $activiti = array();    
            
           
            $activiti['description'] = 'Update profile avatar of employee'.$employee->username;
            $activiti['subject_type'] = 'App\Employee';
            $activiti['causer_id'] = $auth->id;
            $activiti['causer_sid'] = $auth->sid;
            $activiti['log_name'] = $auth->username;
            Activity::create($activiti);

            return response()->json([
                'status' => 'Success',
                'message' => Exception::UPDATED_AVATAR_SUCCESS,
                'image_url' => env('APP_URL').'image/'.$url,
            ],Response::HTTP_OK);
        }else {
            return response()->json(
                Exception::fileUploadFailed(),400);
        }
        
    }
    /**
    * @SWG\Delete(
    *         path="/api/employees/{sid}",
    *         tags={"ADMIN/EMPLOYEE"},
    *         summary="Delete a Employee by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this employee.",
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
    public function destroy($sid)
    {
        $auth = \Auth::user();
        $item = Employee::where('sid', $sid)->first();
        
        if($item->is_active == 1){
            $item->update([
                'deleted_by' => $auth->username,
                'is_active' => 0
                ]);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::DELETE_EMPLOYEE_SUCCESS,
            ],Response::HTTP_OK);
        }else{
            return response()->json(
                Exception::deleteFailed(),400);
        }

    }
    /**
    * @SWG\Get(
    *         path="/api/profile",
    *         tags={"ADMIN/PROFILE"},
    *         description="PROFILE",
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
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
    /**
    * @SWG\Get(
    *         path="/api/mobile/profile",
    *         tags={"MOBILE/PROFILE"},
    *         description="Mobile Profile",
    *         @SWG\Response(
    *             response=200,
    *             description="Success",
    *         ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
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
    public function profile() {
        $auth = \Auth::user();
        return response()->json([
            'status' => 'Success',
            'message' => Exception::SHOW_EMPLOYEE,
            'data' => new EmployeeResource($auth)
        ],Response::HTTP_OK);
       // return new EmployeeResource($auth);
    }

    /**
    * @SWG\Put(
    *         path="/api/profile/updateInfo",
    *         tags={"ADMIN/PROFILE"},
    *         description="UPDATE PROFILE",
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
    *                   required={"first_name","last_name","username","phone_number","email"},
    *                   @SWG\Property(property="first_name", type="string", example="string"),
    *                   @SWG\Property(property="last_name", type="string", example="string"),
    *                   @SWG\Property(property="username", type="string", example="string"),
    *                   @SWG\Property(property="phone_number", type="string", example="string"),
    *                   @SWG\Property(property="email", type="string", example="string"),
    *                   @SWG\Property(property="date_birth", type="string", example="Date_of_birth"),
    *                   @SWG\Property(property="title", type="string", example="string"),
    *                   @SWG\Property(property="address_1", type="string", example="string"),
    *                   @SWG\Property(property="address_2", type="string", example="string"),
    *                   @SWG\Property(property="city", type="string", example="string"),
    *                   @SWG\Property(property="state", type="string", example="string"),
    *                   @SWG\Property(property="zip_code", type="string", example="string")
    *               ),
    *               
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
    /**
    * @SWG\Put(
    *         path="/api/mobile/profile/updateInfo",
    *         tags={"MOBILE/PROFILE"},
    *         description="Update Profile Mobile",
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
    *                   required={"first_name","last_name","username","phone_number","email"},
    *                   @SWG\Property(property="first_name", type="string", example="string"),
    *                   @SWG\Property(property="last_name", type="string", example="string"),
    *                   @SWG\Property(property="username", type="string", example="string"),
    *                   @SWG\Property(property="phone_number", type="string", example="string"),
    *                   @SWG\Property(property="email", type="string", example="string"),
    *                   @SWG\Property(property="date_birth", type="string", example="Date_of_birth"),
    *                   @SWG\Property(property="title", type="string", example="string"),
    *                   @SWG\Property(property="address_1", type="string", example="string"),
    *                   @SWG\Property(property="address_2", type="string", example="string"),
    *                   @SWG\Property(property="city", type="string", example="string"),
    *                   @SWG\Property(property="state", type="string", example="string"),
    *                   @SWG\Property(property="zip_code", type="string", example="string")
    *               ),
    *               
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
    public function updateInfo(Request $request) {
        $auth = \Auth::user();
        $data = $request->all();
        $username = Employee::where('username',$request->username)->whereNotIn('username',[$auth->username])->first();
        if(!empty($username)){
            return response()->json(
                Exception::usernameDuplicated(),400);
        }
        $email = Employee::where('email',$request->email)->whereNotIn('email',[$auth->email])->first();
        if(!empty($email)){
            return response()->json(
                Exception::emailDuplicated(),400);
        }
        $auth->update($request->only('first_name','last_name','username','phone_number','email','date_birth','title','address_1','address_2','city','state','zip_code'));
        //$auth->update($data);
        
        $activiti = array();
        $auth = \Auth::user();
       
        $activiti['description'] = 'Update profile infomation';
        $activiti['subject_type'] = 'App\Employee';
        $activiti['causer_sid'] = $auth->sid;
        $activiti['causer_id'] = $auth->id;
        $activiti['log_name'] = $auth->username;
        Activity::create($activiti);

        return response()->json([
            'status' => 'Success',
            'message' => Exception::UPDATED_EMPLOYEE_SUCCESS,
            'data' => new EmployeeResource($auth)
        ],Response::HTTP_ACCEPTED);
        //return response(new EmployeeResource($auth), Response::HTTP_ACCEPTED);
    }

    /**
    * @SWG\Post(
    *         path="/api/auth/set-new-password",
    *         tags={"ADMIN/AUTH"},
    *         description="Set New Password",
    *         @SWG\Parameter(
    *             name="new_password",
    *             required=true,
    *             in="formData",
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
    public function set_new_password(Request $request)
    {
        $auth = \Auth::user();
        if($auth->first_login == 0){
            $request->validate([
                'new_password' => 'required|min:8',
            ]);
            $auth->update(['password' =>Hash::make($request->input('new_password')),'first_login'=>1]);

            Activity::created([
                'description'=> 'Reset password',
                'subject_type'=> 'App\Reset_password'
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => Exception::UPDATED_AVATAR_SUCCESS,
                'data' => new EmployeeResource($auth),
            ],Response::HTTP_OK);
        }
        else{
            return response()->json(
                Exception::passwordCreatedBefore(),400);
        }
        
    }

    /**
    * @SWG\Put(
    *         path="/api/profile/change-password",
    *         tags={"ADMIN/PROFILE"},
    *         description="Change current password",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               required={"current_password","new_password"},
    *               @SWG\Property(property="current_password", type="string", example="string",),
    *               @SWG\Property(property="new_password", type="string", example="string"),
    *               @SWG\Property(property="confirm_password", type="string", example="string")),
    *               
    *      ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
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
    /**
    * @SWG\Put(
    *         path="/api/mobile/profile/change-password",
    *         tags={"MOBILE/PROFILE"},
    *         description="Change current password",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               required={"current_password","new_password"},
    *               @SWG\Property(property="current_password", type="string", example="string",),
    *               @SWG\Property(property="new_password", type="string", example="string"),
    *               @SWG\Property(property="confirm_password", type="string", example="string")),
    *               
    *      ),
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
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
    public function change_password(UpdatePasswordRequest $request) {
        $auth = \Auth::user();
        if (Hash::check($request->input('current_password'), $auth->password)) {
            $auth->update(['password' =>Hash::make($request->input('new_password')),]);
            $activiti = array();
            $auth = \Auth::user();
           
            $activiti['description'] = 'Changed password';
            $activiti['subject_type'] = 'App\Employee';
            $activiti['causer_sid'] = $auth->sid;
            $activiti['causer_id'] = $auth->id;
            $activiti['log_name'] = $auth->username;
            Activity::create($activiti);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::CHANGE_PASSWORD_SUCCESS,
                'data' => new EmployeeResource($auth),
            ],Response::HTTP_OK);
        }else{
            return response()->json(
                Exception::logoutFailed(),400);
        }
    }
    /**
    * @SWG\Post(
    *         path="/api/profile/upload-avatar",
    *         tags={"ADMIN/PROFILE"},
    *         description="Upload avatar profile",
    *         consumes={"multipart/form-data"},
    *         produces={"application/json"},
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="file_avatar",
    *             description="File name arvatar",
    *             in="formData",
    *             type="file",
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
    /**
    * @SWG\Post(
    *         path="/api/mobile/profile/upload-avatar",
    *         tags={"MOBILE/PROFILE"},
    *         description="Upload avatar profile",
    *         consumes={"multipart/form-data"},
    *         produces={"application/json"},
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="file_avatar",
    *             description="File name arvatar",
    *             in="formData",
    *             type="file",
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
    public function upload_avatar(Request $request) {
        $auth = \Auth::user();
        $uuid_img = Uuid::generate()->string;
        $file = $request->file('file_avatar');
        if(!empty($file)) {
            $request->validate([
                'file_avatar' => 'mimes:jpg,png,gif,tiff,jpeg,heic|max:46080',
            ]);
            $name = 'avata_'.$uuid_img;
            $url = \Storage::putFileAs('avatar',$file,$name. '.'.$file->extension());
            $auth->update(['avatar' => env('APP_URL').'image/'.$url]);
            $activiti = array();
            $auth = \Auth::user();
        
            $activiti['description'] = 'Update profile picture';
            $activiti['subject_type'] = 'App\Employee';
            $activiti['causer_sid'] = $auth->sid;
            $activiti['causer_id'] = $auth->id;
            $activiti['log_name'] = $auth->username;
            Activity::create($activiti);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::UPDATED_AVATAR_SUCCESS,
                'image_url' => env('APP_URL').'image/'.$url,
            ],Response::HTTP_OK);
        }else {
            return response()->json(
                Exception::fileUploadFailed(),400);
        }
        
    }


    /**
    * @SWG\Post(
    *         path="/api/mobile/register_fcm_token",
    *         tags={"MOBILE/FCMTOKEN"},
    *         description="Register FCM Token",
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
    *                   required={"fcm_token"},
    *                   @SWG\Property(property="fcm_token", type="string", example="string"),
    *                   @SWG\Property(property="is_active", type="string", example=true)
    *               ),
    *               
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
    public function register_fcm_token(Request $request){
        $auth = \Auth::user();
        $uuid = Uuid::generate()->string;
        $data = $request->all();
        $data['sid'] = $uuid;
        $data['employee_sid'] = $auth->sid;
        $item = FCM_token::create($data);
        $log_arr = array(
            'location' => __FILE__,
            'function' => 'register_fcm_token',
            '_POST' => !empty($item) ? $item : '',
        );
        debug_log_from_config($log_arr);
        return response()->json([
            'status' => "Success",
            'message' => Exception::CREATED_EMPLOYEE_SUCCESS,
            'data' => new FCMTokenResources($item)
        ],Response::HTTP_CREATED);
    }



    /**
    * @SWG\Delete(
    *         path="/api/mobile/logout",
    *         tags={"MOBILE/EMPLOYEE"},
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="fcm_token",
    *             description="A fcm_tokens identifying this employee.",
    *             in="query",
    *             type="string"
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
    public function logout(Request $request){
        $auth = \Auth::user();
        $employee_sid = $auth->sid;
        if(!empty($request->fcm_token)){
            DB::delete('delete from `fcm_tokens` WHERE fcm_tokens.employee_sid = "' .$employee_sid.'" AND fcm_tokens.fcm_token = "' .$request->fcm_token.'"');
        }
        // DB::delete('delete from `fcm_tokens` WHERE fcm_tokens.employee_sid = '.$v.''.$employee_sid.''.$v.'');
        
        return response()->json([
            'status' => 'Success',
            'message' => Exception::LOGOUT_SUCCESS,
        ],Response::HTTP_OK);
    }

    /**
    * @SWG\Delete(
    *         path="/api/employee/{sid}/logout",
    *         tags={"ADMIN/EMPLOYEE"},
    *         @SWG\Parameter(
    *             name="Authorization",
    *             description="Bearer {access-token}",
    *             in="header",
    *             required=true,
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this employee.",
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
    public function logout_admin($sid){
        $auth = \Auth::user();
       if($auth->role == 'ADMIN'){
            DB::delete('delete from `fcm_tokens` WHERE fcm_tokens.employee_sid = "' .$sid.'"');
            return response()->json([
                'status' => 'Success',
                'message' => Exception::LOGOUT_SUCCESS,
            ],Response::HTTP_OK);
       }else{
        return response()->json(
            Exception::logoutFailed(),400);
       }
        
    }

    
}
