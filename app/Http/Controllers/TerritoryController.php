<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Requests\TerritoryRequests;
use App\Http\Resources\TerritoryResources;
use App\Territory;
use DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Webpatser\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
class TerritoryController extends Controller
{
    
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
    * @SWG\Get(
    *         path="/api/territories",
    *         tags={"ADMIN/Territory"},
    *         summary="Get list Territory",
    *         @SWG\Parameter(
    *             name="search",
    *             description="search territory by name",
    *             in="query",
    *             type="string"
    *         ),
    *         @SWG\Parameter(
    *             name="page",
    *             description="A page number within the paginated result set.",
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
        $territorys = Territory::orderBy('id','desc')->paginate($request->page_size);
        $obj_list = $territorys;

        $search = $request->search;
        if(!empty($search)){
            $search_all = Territory::where('name','LIKE', '%'.$search.'%')
                                    ->paginate($request->page_size);
            $obj_list = $search_all;
        }

        $this->fill_employees_of_territories($obj_list);
        return TerritoryResources::collection($obj_list);
    }
    private function fill_employees_of_territories(&$entity_list){
      $sids_arr = array();
      foreach( $entity_list as $entity){
          $sids_arr[] = "'{$entity->sid}'";
      }
      $sids_str = implode(',', $sids_arr);
      // die($sids_str);

      $query = "
          SELECT employee_to_companies.*, employees.username AS employee_username, employees.full_name AS employee_fullname
          FROM employee_to_companies
              INNER JOIN employees ON employee_to_companies.employee_sid = employees.sid
          WHERE employee_to_companies.territory_sid IN ({$sids_str})
      ";
      // var_dump($query); die();

      $obj_list = DB::select($query);
      $obj_mapping = array();
      foreach($obj_list as $obj){
        $territory_sid = $obj->territory_sid;
        if(empty($obj_mapping[$territory_sid])){
          $obj_mapping[$territory_sid] = array();
        }
        
        $employee_sid = $obj->employee_sid;
        $username = $obj->employee_username;
        $fullname = $obj->employee_fullname;
        $obj_mapping[$territory_sid][] = array(
          'employee_sid' => $employee_sid,
          'username' => $username,
          'fullname' => $fullname,
        );
      }
      
      foreach($entity_list as &$entity){
        $territory_sid = $entity->sid;
        if(!empty($obj_mapping[$territory_sid])){
          $entity->employee_list = $obj_mapping[$territory_sid];
        }
        unset($entity);
      }
      
      unset($entity_list);
    }


    /**
    * @SWG\Post(
    *         path="/api/territories",
    *         tags={"ADMIN/Territory"},
    *         summary="Create a Territory",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               @SWG\Property(property="code", type="string", example="string"),
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="company_sid", type="string", example="string"),
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
    public function store(TerritoryRequests $request)
    {
        $auth = \Auth::user();
        $sid = Uuid::generate()->string;
        $data = $request->all();
        if($request->is_active == 'true'){
            $data['is_active'] = 1;
        }else{
            $data['is_active'] = 0;
        }
        $data['sid'] = $sid;
        $data['created_by'] = $auth->username;
        $territories = Territory::whereName($request->name)->whereCompanySid($request->company_sid)->first();
        if(!empty($territories)){
            return response()->json(
                Exception::companytDuplicated(),400);
        }
        $territories = Territory::whereName($request->name)->whereCode($request->code)->first();
        if(!empty($territories)){
            return response()->json(
                Exception::territoryDuplicated(),400);
        }
        $territories = Territory::whereName($request->name)->first();
        if(!empty($territories)){
            return response()->json(
                Exception::territoryNameDuplicated(),400);
        }
        $territory  = Territory::create($data);
        return response()->json([
            'status' => "Success",
            'message' => Exception::CREATED_TERRITORY_SUCCESS,
            'data' => new TerritoryResources($territory)
        ],Response::HTTP_CREATED);
    }
    /**
    * @SWG\Get(
    *         path="/api/territories/{sid}",
    *         tags={"ADMIN/Territory"},
    *         summary="Get a territory by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this territory.",
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
    public function show($id)
    {
        $territory = Territory::Where('sid',$id)->first();
        return response()->json([
            'status' => "Success",
            'message' => Exception::SHOW_TERRITOTY,
            'data' => new TerritoryResources($territory)
        ],Response::HTTP_OK);
        //return new TerritoryResources($territory);
    }
       /**
    * @SWG\Put(
    *         path="/api/territories/{sid}",
    *         tags={"ADMIN/Territory"},
    *         summary="Put a Territory by UUID",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *               @SWG\Property(property="code", type="string", example="string"),
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="is_active", type="string", example=true)
    *               ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this Territory.",
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
    public function update(Request $request, $id)
    {
        $auth = \Auth::user();
        $territory = Territory::whereSid($id)->first();
        $territories = Territory::whereName($request->name)->whereNotIn('name',[$territory->name])->first();
        if(!empty($territories)){
            return response()->json(
                Exception::territoryNameDuplicated(),400);
        }
        $territories = Territory::whereName($request->name)->whereNotIn('name',[$territory->name])->whereCode($request->code)->whereNotIn('code',[$territory->code])->first();
        if(!empty($territories)){
            return response()->json(
                Exception::territoryDuplicated(),400);
        }
        $data = $request->all();
        $input_data = $data;
        $input_data['id'] = $id;

        $data['updated_by'] = $auth->username;
        if($request->is_active == 'true'){
            $data['is_active'] = 1;
        }else{
            $data['is_active'] = 0;
        }
        $territory->update($data);

        if(empty($data['is_active'])){
            // Update store inactive
           $condition = "territory_sid = '{$id}'";
           $sql = "
                   SELECT count(*) AS count_total
                   FROM stores WHERE {$condition}
                   ";
           $query_list = DB::select($sql);
           $count_store = 0;
           foreach($query_list as $query_obj){
                   $count_store = $query_obj->count_total;
               }
           if($count_store > 0){
               $sql = "
                   UPDATE stores SET is_active = '0'
                   WHERE {$condition}
                   ";
               DB::update($sql);
           }
           
       }
        return response()->json([
            'status' => "Success",
            'message' => Exception::UPDATED_TERRITORY_SUCCESS,
            'data' => new TerritoryResources($territory)
        ],Response::HTTP_ACCEPTED);
        //return response(new TerritoryResources($territory), Response::HTTP_ACCEPTED);
    }

    /**
    * @SWG\Delete(
    *         path="/api/territories/{sid}",
    *         tags={"ADMIN/Territory"},
    *         summary="Delete a territory by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this territory.",
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
        $item = Territory::where('sid', $sid)->first();
        
        if($item->is_active == 1){
            $item->update(['is_active' => 0,'deleted_by' => $auth->username]);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::DELETED_TERRITORY_SUCCESS,
            ],Response::HTTP_OK);
        }else{
            return response()->json(
                Exception::deletedTerritoryFailed(),400);
        }

    }/**
    * @SWG\Get(
    *         path="/api/territory/jobs",
    *         tags={"ADMIN/Territory"},
    *         summary="Get list Job of territory",
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
   public function territory_list_job(Request $request){
    $obj_list = DB::select("
            SELECT jobs.id, jobs.sid, jobs.code, jobs.name, jobs.status
                from jobs 
                inner join job_to_territories on job_to_territories.job_sid = jobs.sid 
                where job_to_territories.company_sid = '{$request->company_sid}'
                and job_to_territories.territory_sid = '{$request->territory_sid}'
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
			'message' => Exception::LIST_JOB_OF_TERRITORY,
			'data' => array_values ($job_mapping)
		],Response::HTTP_OK);
    }
   /**
    * @SWG\Get(
    *         path="/api/territory/stores",
    *         tags={"ADMIN/Territory"},
    *         summary="Get list Store of territory",
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
   public function territory_list_stores(Request $request) {
        $obj_list = DB::select("
                SELECT 
                plans.sid AS plan_sid, plans.status,
                stores.id, stores.sid, stores.name ,stores.phone, stores.climate_region_sid, stores.store_type_sid, stores.address_1, stores.address_2, stores.city, stores.state, stores.zip_code, stores.img_store, stores.file_store, stores.a2_file_number, stores.a2_day_on_file, stores.opening_hour,stores.is_active,stores.created_at,stores.updated_at 
                FROM 
                    plans 
                    INNER JOIN stores on plans.store_sid = stores.sid
                    where stores.company_sid = '{$request->company_sid}'
                    and stores.territory_sid = '{$request->territory_sid}'
                    and plans.status = 'DONE'
            ");
        // return $obj_list;
        $store_mapping = array();
        foreach($obj_list as $obj){
            $store_id = $obj->id;
            if(empty($store_mapping[$store_id])){
                $store_mapping[$store_id] = $obj;
            }
        }

        return response()->json([
            'status' => "Success",
            'message' => Exception::LIST_STORE_OF_TERRITORY,
            'data' => array_values ($store_mapping)
        ],Response::HTTP_OK);
    }

    public function territory_by_job(Request $request){
        $job_sid = $request->job_sid;
        $condition = '';
        if(!empty($job_sid)){
          $condition = "WHERE plans.sid IS NULL OR plans.job_sid = '{$job_sid}'";
        }
        $sql = "
            SELECT territories.*
                FROM job_to_territories
                    INNER JOIN territories ON job_to_territories.territory_sid = territories.sid
            WHERE job_to_territories.job_sid = '{$job_sid}' 
        ";

        $obj_list = DB::select($sql);

        return response()->json([
            'status' => "Success",
            'data' => $obj_list
        ],Response::HTTP_OK);
    }
}
