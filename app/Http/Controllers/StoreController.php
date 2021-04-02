<?php

namespace App\Http\Controllers;
use App\Exceptions\Exception;
use App\Company;
use App\Employee_to_company;
use App\FCM_token;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreateRequest;
use App\Http\Resources\ListPhotoResources;
use App\Http\Resources\ListStoreResources;
use App\Http\Resources\PhotoResources;
use App\Http\Resources\StoreResource;
use App\Job;
use App\Job_to_Territory;
use App\Store;
use App\StoreType;
use App\Territory;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Str;
use Uuid;

class StoreController extends Controller
{
    /**
    * @SWG\Get(
    *         path="/api/stores",
    *         tags={"ADMIN/STORE"},
    *         summary="Get list stores",
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
    *             name="territories_sid",
    *             description="filter territory",
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

        $page_size = $request->page_size;
        if(empty($page_size)){
            $page_size = 15;
        }
        $is_search_filter = false;
        $company_sid = $request->company_sid;
        $territory_sid = $request->territories_sid;
        $search = $request->search;
        $select_custom = DB::raw('CONVERT(stores.name,UNSIGNED INTEGER) AS name_number');
        $select_sql = array($select_custom, 'companies.sid AS company_sid','companies.name AS company_name','territories.sid AS territory_sid','territories.name AS territory_name','store_types.sid AS store_types_sid','store_types.name AS store_types_name','climate_region_store.sid AS climate_region_store_sid','climate_region_store.name AS climate_region_store_name','stores.id', 'stores.sid', 'stores.name' ,'stores.phone', 'stores.climate_region_sid', 'stores.store_type_sid', 'stores.address_1', 'stores.address_2', 'stores.city', 'stores.state', 'stores.zip_code', 'stores.img_store', 'stores.file_store', 'stores.a2_file_number', 'stores.a2_day_on_file', 'stores.opening_hour','stores.is_active','stores.created_at','stores.updated_at');
        $store_list = DB::table('stores')
            ->join('companies', 'stores.company_sid', '=', 'companies.sid')
            ->join('territories', 'stores.territory_sid', '=', 'territories.sid')
            ->join('store_types', 'stores.store_type_sid', '=', 'store_types.sid')
            ->join('climate_region_store', 'stores.climate_region_sid', '=', 'climate_region_store.sid')
            ->select($select_sql);
            
        if (!empty($company_sid)) {
            $is_search_filter = true;
            $store_list->where('stores.company_sid', $company_sid );
        }

        if (!empty($territory_sid)) {
            $is_search_filter = true;
            $store_list->where('stores.territory_sid', $territory_sid );
        }

        if (!empty($search)) {
            $is_search_filter = true;
            // $store_list->where('stores.name','LIKE','%'.$search.'%');
            $store_list->where(function($query)  use ($search) {
                $query->where('stores.name','LIKE','%'.$search.'%')
                ->orWhere('stores.city','LIKE','%'.$search.'%')
                ->orWhere('stores.state',$search);
            });

        }
        if ($is_search_filter === false) {
            $select_custom = DB::raw('CONVERT(stores.name,UNSIGNED INTEGER) AS name_number');
            $select_sql = array($select_custom, 'companies.sid AS company_sid','companies.name AS company_name','territories.sid AS territory_sid','territories.name AS territory_name','store_types.sid AS store_types_sid','store_types.name AS store_types_name','climate_region_store.sid AS climate_region_store_sid','climate_region_store.name AS climate_region_store_name','stores.id', 'stores.sid', 'stores.name' ,'stores.phone', 'stores.climate_region_sid', 'stores.store_type_sid', 'stores.address_1', 'stores.address_2', 'stores.city', 'stores.state', 'stores.zip_code', 'stores.img_store', 'stores.file_store', 'stores.a2_file_number', 'stores.a2_day_on_file', 'stores.opening_hour','stores.is_active','stores.created_at','stores.updated_at');
            $store_list = DB::table('stores')
            ->join('companies', 'stores.company_sid', '=', 'companies.sid')
            ->join('territories', 'stores.territory_sid', '=', 'territories.sid')
            ->join('store_types', 'stores.store_type_sid', '=', 'store_types.sid')
            ->join('climate_region_store', 'stores.climate_region_sid', '=', 'climate_region_store.sid')
            ->select($select_sql);
             $result = $store_list->orderBy('name_number','asc')->paginate($page_size);
            //return $result;
            return ListStoreResources::collection($result);
        } else {
            $result = $store_list->orderBy('name_number','asc')->paginate($page_size);
            return ListStoreResources::collection($result);
        }
        // $store = Store::orderBy('id','desc')->paginate($request->page_size);
        // $com = $request->company_sid;
        // $ter = $request->territories_sid;
        // $r="'";

    

        // $search = $request->search;
        // $u = $search.$com.$ter;
        // if(!empty($u)){
        //     $company = Company::whereName($search)->first();
        //     if(!empty($company)){
        //         $company_sid = $company->sid;
        //         $search_store = Store::where('company_sid',$company_sid)
        //                         ->paginate();
        //     }
        //     $search_store = Store::where('name','LIKE', '%'.$search.'%')
        //                         ->where('company_sid','LIKE', $com)
        //                         ->where('territory_sid','LIKE', $ter)
        //                         ->paginate();
        //     return StoreResource::collection($search_store);
        // }
        // return StoreResource::collection($store);
    }
    /**
    * @SWG\Get(
    *         path="/api/stores/{sid}",
    *         tags={"ADMIN/STORE"},
    *         summary="Get a Store by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this store.",
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

        // $store = Store::Where('sid',$id)->first();
        $store = DB::table('stores')
        ->join('companies', 'stores.company_sid', '=', 'companies.sid')
        ->join('territories', 'stores.territory_sid', '=', 'territories.sid')
        ->join('store_types', 'stores.store_type_sid', '=', 'store_types.sid')
        ->join('climate_region_store', 'stores.climate_region_sid', '=', 'climate_region_store.sid')
        ->select('companies.sid AS company_sid','companies.name AS company_name','territories.sid AS territory_sid','territories.name AS territory_name','store_types.sid AS store_types_sid','store_types.name AS store_types_name','climate_region_store.sid AS climate_region_store_sid','climate_region_store.name AS climate_region_store_name','stores.id', 'stores.sid', 'stores.name' ,'stores.phone', 'stores.climate_region_sid', 'stores.store_type_sid', 'stores.address_1', 'stores.address_2', 'stores.city', 'stores.state', 'stores.zip_code', 'stores.img_store', 'stores.file_store', 'stores.a2_file_number', 'stores.a2_day_on_file', 'stores.opening_hour','stores.is_active','stores.created_at','stores.updated_at')
        ->where('stores.sid', $sid )->get();
        return response()->json([
            'status' => "Success",
            'message' => Exception::SHOW_STORE,
            'data' => new ListStoreResources($store[0])
        ],Response::HTTP_OK);
        //return new StoreResource($store);
    }


    /**
    * @SWG\Post(
    *         path="/api/stores",
    *         tags={"ADMIN/STORE"},
    *         summary="Create a Store",
    *         @SWG\Parameter(
    *           name="data",
    *           in="body",
    *           default="{}",
    *           required=true,
    *           type="object",
    *           @SWG\Schema(
    *               required={"company_sid","territory_sid","name","climate_region_sid","store_type_sid","address_1","opening_hour"},
    *               @SWG\Property(property="company_sid", type="string", example="string"),
    *               @SWG\Property(property="territory_sid", type="string", example="string"),
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="phone", type="string", example="string"),
    *               @SWG\Property(property="climate_region_sid", type="string", example="string"),
    *               @SWG\Property(property="store_type_sid", type="string", example="string"),
    *               @SWG\Property(property="address_1", type="string", example="string"),
    *               @SWG\Property(property="address_2", type="string", example="string"),
    *               @SWG\Property(property="city", type="string", example="string"),
    *               @SWG\Property(property="state", type="string", example="string"),
    *               @SWG\Property(property="a2_file_number", type="string", example="string"),
    *               @SWG\Property(property="a2_day_on_file", type="string", example="string"),  
    *               @SWG\Property(property="zip_code", type="string", example="string"),
    *               @SWG\Property(property="opening_hour", type="string", example="string"),
    *               @SWG\Property(property="is_active", type="string", example=true),
    *           ),
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

    public function store(StoreCreateRequest $request){
        DB::beginTransaction();

        $auth = \Auth::user();
        $store_name = Store::whereName($request->name)->whereTerritorySid($request->territory_sid)->first();
        $territory_sid = $request->territory_sid;
        $company_sid = $request->company_sid;
        if(!empty($store_name)){
            return response()->json(
                Exception::storeNameDuplicated(),400);
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
        
        $data['sid'] = $sid;
        if($request->is_active == 'true'){
            $data['is_active'] = 1;
        }else{
            $data['is_active'] = 0;
        }
        $file_store = $request->file('file_store');
        $data['file_store'] = NULL;
        if(!empty( $file_store)){
            $extension = $file_store->getClientOriginalExtension();
            if(!empty($extension)) {
                $request->validate([
                    'file_store' => 'mimes:doc,docx,pdf,sldx,txt,xlsx,zip|max:46080',
                ]);
                $file_name = $request->a2_file_number.'.'.$extension;
                $request->file('file_store')->move(
                base_path().'/public/file/store', $file_name
                );
                $fullPath = 'store/' . $file_name;
                $data['file_store'] = env('APP_URL').'file/'.$fullPath;
            } else {}
        } else {}
        
        $sid_photo = Uuid::generate()->string;
        $file = $request->file('img_store');
        if(!empty($file)) {
            $request->validate([
                'img_store' => 'mimes:jpg,png,gif,tiff,jpeg,heic|max:46080',
            ]);
            $name ='Store_'.$sid_photo;
            $url = \Storage::putFileAs('store',$file,$name. '.'.$file->extension());
            $data['img_store'] = env('APP_URL').'image/'.$url;
            
        }else {
            $data['img_store'] = NULL;
        }
        $data['created_by'] =  $auth->username;
        $store = Store::create($data);

         // 1 territory chi co 1 job 1 employee
         $job_list = DB::table('jobs')
         ->join('job_to_territories', 'jobs.sid', '=', 'job_to_territories.job_sid')
         ->where('job_to_territories.company_sid', $company_sid)
         ->where('job_to_territories.territory_sid',$territory_sid)
         ->select('jobs.sid','jobs.status')->get();
         $employee = Employee_to_company::whereCompanySid($company_sid)->whereTerritorySid($territory_sid)->first();
         if(!empty($job_list)){
            foreach($job_list as $job){
                $job_sid = $job->sid;
                $is_active = ($job->status == 'NEW' || $job->status == 'INPROGRESS') ? 1 : 0;
                $status = $is_active ? 'NEW' : 'INACTIVE';
    
                // Create plan
                $plan_sid = Uuid::generate()->string;
                DB::table('plans')->insert([
                    'sid' => $plan_sid,
                    'code' => '',
                    'is_manual' => 0,
                    'company_sid' => $company_sid,
                    'territory_sid' => $territory_sid,
                    'store_sid' => $sid,
                    'job_sid' => $job_sid,
                    'employee_sid' => $employee->employee_sid,
                    'status' => $status,
                    'is_active' => $is_active,
                ]);

                // Update plan inactive
                $condition = "job_sid = '{$job_sid}'";
                $sql = "
                        SELECT count(*) AS count_total
                        FROM plans WHERE {$condition}
                        ";
                $query_list = DB::select($sql);
                $count_store = 0;
                foreach($query_list as $query_obj){
                    $count_store = $query_obj->count_total;
                }

                $condition = "sid = '{$job_sid}'";
                $sql = "
                UPDATE jobs SET store_count = {$count_store} WHERE {$condition}
                ";
                DB::update($sql);
            }
        }
        DB::commit();

        return response()->json([
            'status' => "Success",
            'message' => Exception::ADD_STORE_SUCCESS,  
            'data' => $store
        ],Response::HTTP_CREATED);
        //return response(new StoreResource($store), Response::HTTP_CREATED);
    }
    /**
    * @SWG\Post(
    *         path="/api/stores/{sid}/photo",
    *         tags={"ADMIN/STORE"},
    *         summary="Create photo a Store",
    *         @SWG\Parameter(
    *             name="img_store",
    *             description="Store Photo",
    *             in="formData",
    *             type="file",
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this store.",
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
    public function store_photo(Request $request,$sid){

        $store = Store::whereSid($sid)->first();

        $sid_photo = Uuid::generate()->string;
        $sid = Uuid::generate()->string;
        // return $urlfile;
        $file = $request->file('img_store');
        
        if(!empty($file)) {
            $request->validate([
                'img_store' => 'mimes:jpg,png,gif,tiff,jpeg,heic|max:46080',
            ]);
            $name ='Store_'.$sid_photo;
            $url = \Storage::putFileAs('store',$file,$name. '.'.$file->extension());
            
            $store->update(['img_store' => env('APP_URL').'image/'.$url]);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::ADD_STORE_FILE_PHOTO_SUCCESS,
                'image_url' => env('APP_URL').'image/'.$url,
            ],Response::HTTP_OK);
        }else {
            return response()->json(
                Exception::addStoreFilePhotoFailed(),400);
        }
        
        //return response($store, Response::HTTP_CREATED);
    }
    /**
    * @SWG\Post(
    *         path="/api/stores/{sid}/file",
    *         tags={"ADMIN/STORE"},
    *         summary="Create file a Store",
    *         @SWG\Parameter(
    *             name="file_store",
    *             description="File store",
    *             in="formData",
    *             type="file",
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this store.",
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
    public function store_file(Request $request,$sid){

        $store = Store::whereSid($sid)->first();
       // $sid_photo = Uuid::generate()->string;
        
        
        $extension = $request->file('file_store')->getClientOriginalExtension();
        if(!empty($extension)) {
            $request->validate([
                'file_store' => 'mimes:doc,docx,pdf,sldx,txt,xlsx,zip|max:46080',
            ]);
            if(!empty($store->a2_file_number)){
                $file_name = $store->a2_file_number.'.'.$extension;
            }else{
                $name_file = Uuid::generate()->string;
                $file_name = $name_file.'.'.$extension;
            }
            $request->file('file_store')->move(
            base_path().'/public/file/store', $file_name
            );
            $fullPath = 'store/' . $file_name;

            $store->update(['file_store' => env('APP_URL').'file/'.$fullPath]);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::ADD_STORE_FILE_SUCCESS,
                'file_url' => env('APP_URL').'file/'.$fullPath,
            ],Response::HTTP_OK);
        }else {
            return response()->json(
                Exception::addStoreFileFailed(),400);
        }
    }
    /**
    * @SWG\Put(
    *         path="/api/stores/{sid}",
    *         tags={"ADMIN/STORE"},
    *         summary="Put a Store by UUID",
    *         @SWG\Parameter(
    *           name="data",
    *           in="body",
    *           default="{}",
    *           required=true,
    *           type="object",
    *           @SWG\Schema(
    *               required={"company_sid","territory_sid","name","climate_region_sid","store_type_sid","address_1","opening_hour"},
    *               @SWG\Property(property="company_sid", type="string", example="string"),
    *               @SWG\Property(property="territory_sid", type="string", example="string"),
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="phone", type="string", example="string"),
    *               @SWG\Property(property="climate_region_sid", type="string", example="string"),
    *               @SWG\Property(property="store_type_sid", type="string", example="string"),
    *               @SWG\Property(property="address_1", type="string", example="string"),
    *               @SWG\Property(property="address_2", type="string", example="string"),
    *               @SWG\Property(property="city", type="string", example="string"),
    *               @SWG\Property(property="state", type="string", example="string"),
    *               @SWG\Property(property="a2_file_number", type="string", example="string"),
    *               @SWG\Property(property="a2_day_on_file", type="string", example="string"),  
    *               @SWG\Property(property="zip_code", type="string", example="string"),
    *               @SWG\Property(property="opening_hour", type="string", example="string"),
    *               @SWG\Property(property="is_active", type="string", example=true),
    *           ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this store.",
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
    *         path="/api/stores/{sid}",
    *         tags={"ADMIN/STORE"},
    *         summary="Put a Store by UUID",
    *         @SWG\Parameter(
    *           name="data",
    *           in="body",
    *           default="{}",
    *           required=true,
    *           type="object",
    *           @SWG\Schema(
    *               required={"company_sid","territory_sid","name","climate_region_sid","store_type_sid","address_1","opening_hour"},
    *               @SWG\Property(property="company_sid", type="string", example="string"),
    *               @SWG\Property(property="territory_sid", type="string", example="string"),
    *               @SWG\Property(property="name", type="string", example="string"),
    *               @SWG\Property(property="phone", type="string", example="string"),
    *               @SWG\Property(property="climate_region_sid", type="string", example="string"),
    *               @SWG\Property(property="store_type_sid", type="string", example="string"),
    *               @SWG\Property(property="address_1", type="string", example="string"),
    *               @SWG\Property(property="address_2", type="string", example="string"),
    *               @SWG\Property(property="city", type="string", example="string"),
    *               @SWG\Property(property="state", type="string", example="string"),
    *               @SWG\Property(property="a2_file_number", type="string", example="string"),
    *               @SWG\Property(property="a2_day_on_file", type="string", example="string"),  
    *               @SWG\Property(property="zip_code", type="string", example="string"),
    *               @SWG\Property(property="opening_hour", type="string", example="string"),
    *               @SWG\Property(property="is_active", type="string", example=true),
    *           ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this store.",
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


    private function store_check_no_plan($store){
        $store_sid = $store->sid;
        $condition = "store_sid = '{$store_sid}' AND status <> 'NEW'";
        $sql = "
            SELECT count(*) AS count_total
            FROM plans WHERE {$condition}
        ";
        // die($sql);
        $query_list = DB::select($sql);
        $count_plan = 0;
        foreach($query_list as $query_obj){
            $count_plan = $query_obj->count_total;
        }

        return $count_plan == 0;
    }
    private function store_disable_related_plan($store){
        $store_sid = $store->sid;
        $condition = "store_sid = '{$store_sid}'";
        $sql = "
            UPDATE plans SET is_active = '0', status = 'INACTIVE'
            WHERE {$condition}
            ";
        DB::update($sql);
    }
    public function update(Request $request, $id){
        DB::beginTransaction();

        $auth = \Auth::user();
        $flag_check = false;

        $return_data = response()->json(
            Exception::dataInvalid(),500);

        $store = Store::whereSid($id)->first();
        $store_name = Store::whereName($request->name)->whereTerritorySid($request->territory_sid)->whereNotIn('name',[$store->name])->first();
        if(!empty($store_name)){
            return response()->json(
                Exception::storeNameDuplicated(),400);
        }
        $data = $request->all();
        $input_data = $data;
        $territory_sid = $request->territory_sid;
        $company_sid = $request->company_sid;
        //$is_active = (bool)$request->is_active;
        $input_data['id'] = $id;
        //$input_data['sid'] = $store_sid;
        //$input_data['is_active'] = $is_active;

        $flag_update = false;
        // var_dump($data);exit();
        if(!empty($data['territory_sid'])){
            $value = $data['territory_sid'];
            $value_old = $store->territory_sid;
            if($value != $value_old){
                $flag_update = true;
            }
        }

        if($request->is_active == 'true'){
            $data['is_active'] = 1;
            
        }else{
            $data['is_active'] = 0;
        }
        if($flag_update){
            $flag_check_no_plan = $this->store_check_no_plan($store);
            $flag_check = $flag_check_no_plan;
        } else {
            $flag_check = true; // No update => $flag_check = true
        }
        // die('$flag_check = '.$flag_check);
        if($flag_check){
            //$company->update($request->only('name', 'tax', 'email', 'fax', 'phone', 'address_1', 'address_2', 'city', 'state', 'zip_code','is_active'));
            $data['updated_by'] = $auth->username;
            $store->update($data);
            if(empty($data['is_active'])){
                // Update plan inactive
                $condition = "store_sid = '{$id}'";
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
                   $this->store_disable_related_plan($store);
                }
            }

            $flag_create_plan = false;
            if($flag_update){
                $flag_create_plan = true;
            } else {
                $flag_check_no_plan = $this->store_check_no_plan($store);
                $flag_create_plan = $flag_check_no_plan;
            }
            if($flag_create_plan){
                $this->store_disable_related_plan($store);

                // 1 territory chi co 1 job 1 employee
                $job_list = DB::table('jobs')
                ->join('job_to_territories', 'jobs.sid', '=', 'job_to_territories.job_sid')
                ->where('job_to_territories.company_sid', $company_sid)
                ->where('job_to_territories.territory_sid',$territory_sid)
                ->select('jobs.sid','jobs.status')->get();
                $employee = Employee_to_company::whereCompanySid($company_sid)->whereTerritorySid($territory_sid)->first();
                if(!empty($job_list)){
                    foreach($job_list as $job){
                        $job_sid = $job->sid;
                        $is_active = ($job->status == 'NEW' || $job->status == 'INPROGRESS') ? 1 : 0;
                        $status = $is_active ? 'NEW' : 'INACTIVE';
            
                        // Create plan
                        $plan_sid = Uuid::generate()->string;
                        DB::table('plans')->insert([
                            'sid' => $plan_sid,
                            'code' => '',
                            'is_manual' => 0,
                            'company_sid' => $company_sid,
                            'territory_sid' => $territory_sid,
                            'store_sid' => $id,
                            'job_sid' => $job_sid,
                            'employee_sid' => $employee->employee_sid,
                            'status' => $status,
                            'is_active' => $is_active,
                        ]);

                        // Update plan inactive
                        $condition = "job_sid = '{$job_sid}'";
                        $sql = "
                                SELECT count(*) AS count_total
                                FROM plans WHERE {$condition}
                                ";
                        $query_list = DB::select($sql);
                        $count_store = 0;
                        foreach($query_list as $query_obj){
                            $count_store = $query_obj->count_total;
                        }

                        $condition = "sid = '{$job_sid}'";
                        $sql = "
                        UPDATE jobs SET store_count = {$count_store} WHERE {$condition}
                        ";
                        DB::update($sql);
                    }
                }
            }

            DB::commit();

            $return_data = response()->json([
                'status' => "Success",
                'message' => Exception::UPDATE_STORE_SUCCESS,
                'data' => $store
            ],Response::HTTP_ACCEPTED);
        } else {
            $return_data = response()->json(
                Exception::store_linked_to_plan_error(),500);
        }
        
        return $return_data;
    }

    /**
    * @SWG\Delete(
    *         path="/api/stores/{sid}",
    *         tags={"ADMIN/STORE"},
    *         summary="Delete a Store by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this store.",
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
        $auth = \Auth::user();
        $item = Store::where('sid', $sid)->first();
        
        if($item->is_active == 1){
            $item->update(['is_active' => 0,'deleted_by' => $auth->username]);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::DESTROY_STORE_SUCCESS,
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'status' => false,
                'message' => Exception::DESTROY_STORE_FAILED,
            ],400);
        }
    }

      /**
    * @SWG\Get(
    *         path="/api/store/photos",
    *         tags={"ADMIN/STORE"},
    *         summary="Get list Photo of Store",
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
    public function store_list_photos(Request $request){
        // $query = "
        //         SELECT 
        //         plans.sid AS plan_sid,
        //         photo.id, photo.sid,photo.plan_actual_sid,photo.img_photo, photo.elevation_code, photo.name, photo.description,photo.created_by,photo.updated_by,photo.created_at,photo.updated_at
        //         FROM 
        //             plans 
        //             INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid
        //             INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid
        //         WHERE plans.store_sid = '{$request->store_sid}'
        //         and plans.territory_sid = '{$request->territory_sid}'
        //         and plans.company_sid = '{$request->company_sid}'
        //     ";
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
                ->where('plans.store_sid', $request->store_sid )
                ->where('plans.territory_sid', $request->territory_sid )
                ->where('plans.company_sid', $request->company_sid )
                ->orderBy('id','desc')->paginate($page_size);
            //return $photo;
            // $stores_list = DB::select($query);
            return ListPhotoResources::collection($photo);
        
    }
}