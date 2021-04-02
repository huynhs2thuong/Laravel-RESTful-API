<?php

namespace App\Http\Controllers;
use App\Exceptions\Exception;
use App\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyCreateRequest;
use App\Http\Resources\CompanyResource;
use App\Job;
use App\Job_to_Territory;
use App\Territory;
use DB;
use Dotenv\Result\Success;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Uuid;

class CompanyController extends Controller
{

    /**
    * @SWG\Get(
    *         path="/api/companies",
    *         tags={"ADMIN/COMPANY"},
    *         summary="Get list companies",
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
    *             description="Search company",
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
    public function index(Request $request){
        $company = Company::orderBy('id','desc')->paginate($request->page_size);

        $search = $request->search;
        if(!empty($search)){
            $r="'";
                    $search_company = Company::where('name','LIKE', '%'.$search.'%')
                                    // ->orWhere('phone','LIKE', '%'.$search.'%')
                                    // ->orWhere('state','LIKE', '%'.$search.'%')
                                    // ->orWhere('city','LIKE', '%'.$search.'%')
                                    ->paginate();
            return CompanyResource::collection($search_company);
        }
        return CompanyResource::collection($company);
    }
    /**
    * @SWG\Get(
    *         path="/api/companies/{sid}",
    *         tags={"ADMIN/COMPANY"},
    *         summary="Get a Company by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this company.",
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
    public function show($id){

        $company = Company::Where('sid',$id)->first();
        return response()->json([
            'status' => "Success",
            'message' => Exception::SHOW_COMPANY,
            'data' => new CompanyResource($company),
        ],Response::HTTP_OK);
        // return new CompanyResource(Company::find($id));
    }


    /**
    * @SWG\Post(
    *         path="/api/companies",
    *         tags={"ADMIN/COMPANY"},
    *         summary="Create a Company",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               required={"name","phone","email"},
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="tax", type="string", example="string"),
    *               @SWG\Property(property="fax", type="string", example="string"),
    *               @SWG\Property(property="phone", type="string", example="string"),
    *               @SWG\Property(property="email", type="string", example="string"),
    *               @SWG\Property(property="address_1", type="string", example="string"),
    *               @SWG\Property(property="address_2", type="string", example="string"),
    *               @SWG\Property(property="city", type="string", example="string"),
    *               @SWG\Property(property="state", type="string", example="string"),
    *               @SWG\Property(property="zip_code", type="string", example="string"),
    *               @SWG\Property(property="is_active", type="string", example=true)
    *           ),
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

    public function store(CompanyCreateRequest $request){
        $company_name = Company::whereName($request->name)->first();
        if(!empty($company_name)){
            return response()->json(
                Exception::companyDuplicated(),400);
        }
        $company_email = Company::whereEmail($request->email)->first();
        if(!empty($company_email)){
            return response()->json(
                Exception::emailCompanyDuplicated(),400);
        }
        $sid = Uuid::generate()->string;
        $data = $request->all();
        $auth=\Auth::user();
        $data['created_by'] = $auth->username;
        $data['sid'] = $sid;
        if($request->is_active == 'true'){
            $data['is_active'] = 1;
        }else{
            $data['is_active'] = 0;
        }
        
        $company = Company::create($data);
        //$company = Company::create(['sid' => $sid] + $request->only('name', 'tax', 'email', 'fax', 'phone', 'address_1', 'address_2', 'city', 'state', 'zip_code', 'is_active'));
        return response()->json([
            'status' => "Success",
            'message' => Exception::ADD_COMPANY_SUCCESS,
            'data' => new CompanyResource($company),
        ],Response::HTTP_OK);
        //return response($company, Response::HTTP_CREATED);
    }


/**
    * @SWG\Put(
    *         path="/api/companies/{sid}",
    *         tags={"ADMIN/COMPANY"},
    *         summary="Put a Company by UUID",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *               required={"name","phone","email"},
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="tax", type="string", example="string"),
    *               @SWG\Property(property="fax", type="string", example="string"),
    *               @SWG\Property(property="phone_number", type="string", example="string"),
    *               @SWG\Property(property="email", type="string", example="string"),
    *               @SWG\Property(property="address_1", type="string", example="string"),
    *               @SWG\Property(property="address_2", type="string", example="string"),
    *               @SWG\Property(property="city", type="string", example="string"),
    *               @SWG\Property(property="state", type="string", example="string"),
    *               @SWG\Property(property="zip_code", type="string", example="string"),
    *               @SWG\Property(property="is_active", type="string", example=true)
    *               ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this company.",
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
    *         path="/api/companies/{sid}",
    *         tags={"ADMIN/COMPANY"},
    *         summary="Put a Company by UUID",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *               required={"name","phone","email"},
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="tax", type="string", example="string"),
    *               @SWG\Property(property="fax", type="string", example="string"),
    *               @SWG\Property(property="phone_number", type="string", example="string"),
    *               @SWG\Property(property="email", type="string", example="string"),
    *               @SWG\Property(property="address_1", type="string", example="string"),
    *               @SWG\Property(property="address_2", type="string", example="string"),
    *               @SWG\Property(property="city", type="string", example="string"),
    *               @SWG\Property(property="state", type="string", example="string"),
    *               @SWG\Property(property="zip_code", type="string", example="string"),
    *               @SWG\Property(property="is_active", type="string", example=true)
    *               ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this company.",
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

    public function update(Request $request, $id){

        $company = Company::whereSid($id)->first();
        $company_name = Company::whereName($request->name)->whereNotIn('name',[$company->name])->first();
        if(!empty($company_name)){
            return response()->json(
                Exception::companyDuplicated(),400);
        }
        $company_email = Company::whereEmail($request->email)->whereNotIn('email',[$company->email])->first();
        if(!empty($company_email)){
            return response()->json(
                Exception::emailCompanyDuplicated(),400);
        }
        //return $company;
        $data = $request->all();
        if($request->is_active == 'true'){
            $data['is_active'] = 1;
        }else{
            $data['is_active'] = 0;
        }
        // return $data;
        //$company->update($request->only('name', 'tax', 'email', 'fax', 'phone', 'address_1', 'address_2', 'city', 'state', 'zip_code','is_active'));
        $auth=\Auth::user();
        $data['updated_by'] = $auth->username;
        
        $company->update($data);

        if(empty($data['is_active'])){
            // Update plan inactive
           $condition = "company_sid = '{$id}'";
           $sql_store = "
                   SELECT count(*) AS count_total
                   FROM stores WHERE {$condition}
                   ";
           $query_list = DB::select($sql_store);
           $count_store = 0;
           foreach($query_list as $query_obj){
                   $count_store = $query_obj->count_total;
               }
           if($count_store > 0){
               $sql_store = "
                   UPDATE stores SET is_active = '0'
                   WHERE {$condition}
                   ";
               DB::update($sql_store);
           }
           // territory
           $sql_territory = "
                   SELECT count(*) AS count_total
                   FROM territories WHERE {$condition}
                   ";
           $query_list = DB::select($sql_territory);
           $count_terri = 0;
           foreach($query_list as $query_obj){
                   $count_terri = $query_obj->count_total;
               }
           if($count_terri > 0){
               $sql_territory = "
                   UPDATE territories SET is_active = '0'
                   WHERE {$condition}
                   ";
               DB::update($sql_territory);
           }

           // plan
           $sql_plan = "
                   SELECT count(*) AS count_total
                   FROM plans WHERE {$condition}
                   ";
           $query_list = DB::select($sql_plan);
           $count_plan = 0;
           foreach($query_list as $query_obj){
                   $count_plan = $query_obj->count_total;
               }
           if($count_plan > 0){
               $sql_plan = "
                   UPDATE plans SET is_active = '0', status = 'INACTIVE'
                   WHERE {$condition}
                   ";
               DB::update($sql_plan);
           }
        }
        return response()->json([
            'status' => "Success",
            'message' => Exception::UPDATE_COMPANY_SUCCESS,
            'data' => new CompanyResource($company),
        ],Response::HTTP_OK);
        //return response(new CompanyResource($company), Response::HTTP_ACCEPTED);
    
    }

    /**
    * @SWG\Delete(
    *         path="/api/companies/{sid}",
    *         tags={"ADMIN/COMPANY"},
    *         summary="Delete a Company by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this company.",
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

    public function destroy($sid){

        $item = Company::where('sid', $sid)->first();
        $auth=\Auth::user();
        if($item->is_active == 1){
            $item->update(['is_active' => 0,
                            'deleted_by' => $auth->username,
            ]);
            return response()->json([
                'status' => "Success",
                'message' => Exception::DESTROY_COMPANY_SUCCESS,
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'status' => "Failed",
                'message' => Exception::DESTROY_COMPANY_FAILED,
            ],Response::HTTP_NOT_FOUND);
        }
    }
    /**
    * @SWG\Get(
    *         path="/api/companies/{sid}/territories",
    *         tags={"ADMIN/COMPANY"},
    *         summary="Company to Territory by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this company.",
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
    public function company_to_territory($sid){

        $territories =Territory::where('company_sid',$sid)->join('companies', 'territories.company_sid', '=', 'companies.sid')->get(['companies.sid AS company_sid' ,'companies.name AS company_name','territories.sid','territories.code','territories.name','territories.is_active']);
        return response() ->json([
            'status'=> "Success",
            'message'=> Exception::GET_COMPANY_TO_TERRITORY,
            'data'=>$territories
        ],Response::HTTP_OK);

    }

    /**
    * @SWG\Get(
    *         path="/api/companies/{sid}/jobs",
    *         tags={"ADMIN/COMPANY"},
    *         summary="Job to Company by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this company.",
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
    public function company_to_jobs($sid){
        $company_to_jobs = Job::join('companies', 'jobs.company_sid', '=', 'companies.sid')->where('jobs.company_sid',$sid)->get(['jobs.id','jobs.sid','jobs.code','jobs.name','jobs.status','companies.sid AS company_sid','companies.name AS company_name']);
        return response() ->json([
            'status'=> "Success",
            'message'=> Exception::GET_COMPANY_TO_JOB,
            'data'=>$company_to_jobs
        ],Response::HTTP_OK);
        
    }

    


    
}
