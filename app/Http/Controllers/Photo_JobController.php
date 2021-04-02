<?php

namespace App\Http\Controllers;

use App\Company;
use App\Employee;
use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\ListPhotoResources;
//use App\Http\Requests\PhotoCreateRequest;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\PhotoResources;
use App\Photo;
use App\Photo_job_tag;
use App\Plan;
use App\Plan_actual;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;
use Str;
use Uuid;

class Photo_JobController extends Controller
{
     /**
    * @SWG\Get(
    *         path="/api/photos",
    *         tags={"ADMIN/PHOTO"},
    *         summary="Get list Photo",
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
    public function index(Request $request)
    {
        // TEST
        $data =$this->search_filter($request);
        return $data;
    }
        // END TEST
    //     $photo_list = Photo::orderBy('id','desc')->paginate($request->page_size);


    //     if(!empty($request->search) || !empty($request->company_sid)){
    //         // $search_store = Photo::where('name','LIKE', '%'.$request->search.'%')->join('plan_actual', 'photo.plan_actual_sid', '=', 'plan_actual.sid')->join('plans', 'plan_actual.sid', '=', 'plans.plan_actual_sid')
    //         // ->where('plans.company_sid','LIKE', $request->company_sid)
    //         // ->where('plans.territory_sid','LIKE', $request->territory_sid)
    //         // ->where('plans.employee_sid','LIKE', $request->employee_sid)
    //         // ->where('plans.store_sid','LIKE', $request->store_sid)
    //         // ->where('plans.job_sid','LIKE', $request->job_sid)
    //         // ->where('photo.elevation_code','LIKE', $request->elevation_code)
    //         // ->paginate();
    //         $search_plan = Plan::where('plans.company_sid','LIKE', $request->company_sid)
    //         ->where('territory_sid','LIKE', $request->territory_sid)
    //         ->where('employee_sid','LIKE', $request->employee_sid)
    //         ->where('store_sid','LIKE', $request->store_sid)
    //         ->where('job_sid','LIKE', $request->job_sid)
    //         ->get();
    //         if(!empty($search_plan)){
    //             $data = array();
    //             foreach ($search_plan as $plan){
    //                 $query = "
    //                     SELECT 
    //                     plans.sid AS plan_sid, 
    //                     photo.id, photo.sid, photo.img_photo, photo.elevation_code, photo.name, photo.description,
    //                     photo_job_tag.photo_tag_sid
    //                     FROM 
    //                         plans 
    //                         INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid 
    //                         INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid 
    //                         INNER JOIN photo_job_tag ON photo.sid = photo_job_tag.photo_sid 
    //                     WHERE plans.sid = '{$plan->sid}'

    //                 ";
    //                 $obj_list = DB::select($query);
    //                 $photo_mapping = array();
    //                 foreach($obj_list as $obj){
    //                     $photo_id = $obj->id;
    //                     $photo_tag_sid = $obj->photo_tag_sid;
    //                     unset($obj->photo_tag_sid);
    //                     if(empty($photo_mapping[$photo_id])){
    //                         $obj->photo_tags_sid = array();
    //                         $photo_mapping[$photo_id] = $obj;
    //                     }
    //                     $photo_mapping[$photo_id]->photo_tags_sid[] = $photo_tag_sid;
    //                 }
    //                 array_push($data,array_values ($photo_mapping));
    //             }
    //             return array_values($data);
    //         }
    //         // $query = "
    //         // SELECT 
    //         // plans.sid AS plan_sid, 
    //         // photo.id, photo.sid, photo.img_photo, photo.elevation_code, photo.name, photo.description
    //         // FROM 
    //         //     plans 
    //         //     INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid 
    //         //     INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid 
    //         // WHERE plans.company_sid = '{$request->company_sid}'
    //         // OR plans.territory_sid = '{$request->territory_sid}'
    //         // OR plans.employee_sid = '{$request->employee_sid}'
    //         // OR plans.store_sid = '{$request->store_sid}'
    //         // OR plans.job_sid = '{$request->job_sid}'
    //         // OR photo.name = '{$request->search}'
    //         // OR photo.elevation_code = '{$request->elevation_code}'

    //         // ";
    //         //$obj_list = DB::select($query);
    //         //return $obj_list;
    //     }
      
    //    return PhotoResources::collection($photo_list);
    //}

    function search_filter($request) {
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
            // $photo_list->where('photo.description', 'LIKE','%'.$description.'%' );
            // $photo_list->where(function ($query) use ($description) {
            //     $query->where('photo.description', 'LIKE','%'.$description.'%' )
            //           ->orWhere('stores.name', 'LIKE','%'.$description.'%' );
            // });
            $photo_list->where(function ($query) use ($description) {
                $query->where('photo.name', 'LIKE','%'.$description.'%' )
                      ->orWhere('photo.description', 'LIKE','%'.$description.'%');
            });
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

        // ko co search thi chay default
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
            $result = $photo_list->orderBy('id','desc')->paginate($page_size);
            return ListPhotoResources::collection($result);
        } else {
            $result = $photo_list->orderBy('id','desc')->paginate($page_size);
        }
        
        return ListPhotoResources::collection($result);
    }

    /**
    * @SWG\Post(
    *         path="/api/photos",
    *         tags={"ADMIN/PHOTO"},
    *         summary="Create a Photo",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
    *                   @SWG\Property(property="name", type="string", example="string"),
    *                   @SWG\Property(property="description", type="string", example="string"),
    *                   @SWG\Property(property="company_sid", type="string", example="string"),
    *                   @SWG\Property(property="territory_sid", type="string", example="string"),
    *                   @SWG\Property(property="store_sid", type="string", example="string"),
    *                   @SWG\Property(property="job_sid", type="string", example="string"),
    *                   @SWG\Property(property="elevation_code", type="string", example="string"),
    *                   @SWG\Property(property="test_metrie", type="array",@SWG\Items(type="string")),
    *                   @SWG\Property(property="created_by", type="string", example="string"),
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
    public function store(Request $request)
    {
        $sid = Uuid::generate()->string;
        $data = $request->all();
        $auth = \Auth::user();
        $data['sid'] = $sid;
        
       
        if($request->is_active == 'true'){
            $data['is_active'] = 1;
        }else{
            $data['is_active'] = 0;
        }
        $data['elevation_code'] = $request->elevation_code;
        $file = $request->file('img_photo');
        $sid_photo  = Uuid::generate()->string;
        if(!empty($file)) {
            $request->validate([
                'img_photo' => 'mimes:jpg,png,gif,tiff,jpeg,heic|max:46080',
            ]);
            if(!empty($request->name)){
                $name = $request->name;
            }else{
                $name ='photo-'.$sid_photo;
            }
            $url = \Storage::putFileAs('photo',$file,$name. '.'.$file->extension());
            $data['name'] = $name;
            $data['img_photo' ] = env('APP_URL').'image/'.$url;
        }else {
            $data['img_photo' ] = Null;
        }
        $employee = $request->created_by;
        if(!empty($employee)){
            $employee_name = Employee::whereSid($request->created_by)->first();
           
            $data['created_by'] = $employee_name->username;
            
        }else{
            $data['created_by'] = $auth->username;
        }
        $photo_tags = $request->test_metrie;
        
        $plan = Plan::whereStoreSid($request->store_sid)->whereTerritorySid($request->territory_sid)->whereCompanySid($request->company_sid)->whereJobSid($request->job_sid)->whereStatus('DONE')->first();
        if(!empty($plan)){
            $plan_actual = Plan_actual::wherePlanSid($plan->sid)->first();
            $data['plan_actual_sid'] = $plan_actual->sid;
            foreach($photo_tags as $photo_tag) {
                DB::table('photo_job_tag')->insert([
                    'photo_sid' => $sid,
                    'photo_tag_sid' => $photo_tag,
                ]);
            }
            $photo = Photo::create($data);
            return response()->json([
                'status' => "Success",
                'message' => Exception::CREATED_PHOTO_SUCCESS,
                'data' => $photo
            ],Response::HTTP_CREATED);
        }else{
            return response()->json(
                Exception::createdPhotoFailed(),400);
        }
    }

    /**
    * @SWG\Get(
    *         path="/api/photos/{sid}",
    *         tags={"ADMIN/PHOTO"},
    *         summary="Get a Photo by UUID",
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this photo.",
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
        //$photo = Photo::Where('sid',$sid)->first();
    
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
            ->where('photo.sid', $sid )->get();
           // return $photo;
        return response()->json([
            'status' => "Success",
            'message' => Exception::SHOW_PHOTO,
            'data' => new ListPhotoResources($photo[0])
        ],Response::HTTP_OK);
    }

    /**
    * @SWG\Post(
    *         path="/api/mobile/photo",
    *         tags={"MOBILE/PHOTO"},
    *         summary="Create a Photo with code: FR, RT, RE, LT",
    *         @SWG\Parameter(
    *             name="img_photo",
    *             description="Photo",
    *             in="formData",
    *             type="file",
    *         ),
    *         @SWG\Parameter(
    *             name="data",
    *             description="JSON of data: plan_sid(string), flag_reset(number), elevation_code(string), name(string), description(string), photo_tags_sid(array)",
    *             in="formData",
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
    public function store_photo_mobile(Request $request)
    {
        $return_data = response()->json(Exception::dataInvalid(), 500); // default

        DB::beginTransaction();
        try {
            $auth = \Auth::user();

            $input_data = $request->all();
            $input_data_old = $input_data;
            //var_dump($input_data);die();
            // TODO - Fix undefined
            $input_data_temp = array();
            foreach($input_data as $input_data_key => $input_data_value){
                if($input_data_value != 'undefined'){
                    $input_data_temp[$input_data_key] = $input_data_value;
                }
            }
            $input_data = $input_data_temp;
            // var_dump($input_data);die();

            $data_json_str = $input_data['data'];
            if(!empty($data_json_str)){
                $data_json = json_decode($data_json_str, true);
            }
            if(!empty($data_json)){
                // var_dump($data_json);die();

                $plan_sid = $data_json['plan_sid'];
                if($plan_sid){
                    $plan_actual = Plan_actual::wherePlanSid($plan_sid)->first();
                    // var_dump($plan_actual);die();
                    if(!empty($plan_actual)){
                        // TODO
                        $plan_actual_sid = $plan_actual->sid;
                    } else {
                        // Create plan_actual
                        $plan_actual_sid = Uuid::generate()->string;
                        DB::table('plan_actual')->insert([
                            'sid' => $plan_actual_sid,
                            'plan_sid' => $plan_sid,
                            'actual_date' => date('Y-m-d'),
                            'is_manual' => 0,
                            'status' => 'NEW', // TODO
                        ]);
                    }
                }
                
                if(!empty($plan_actual_sid)){
                    // Reset
                    if(!empty($data_json['flag_reset'])){
                        // Delete all photo_tags_sid
                        $sql = "
                            DELETE photo_job_tag 
                            FROM photo_job_tag INNER JOIN photo ON photo_job_tag.photo_sid = photo.sid
                            WHERE photo.plan_actual_sid = '{$plan_actual_sid}'
                        ";
                        DB::delete($sql);

                        // Delete photo
                        $sql = "
                            DELETE 
                            FROM photo
                            WHERE photo.plan_actual_sid = '{$plan_actual_sid}'
                        ";
                        DB::delete($sql);
                    }

                    //var_dump($plan_actual_sid);die();
                    $sid = Uuid::generate()->string;

                    $data['sid'] = $sid;
                    $data['plan_actual_sid'] = $plan_actual_sid;
                    // if($request->is_active == 'true'){
                    //     $data['is_active'] = 1;
                    // }else{
                    //     $data['is_active'] = 0;
                    // }
                    $data['elevation_code'] = $data_json['elevation_code'];
                    $data['name'] = $data_json['name'];
                    $data['description'] = $data_json['description'];
        
                    
                    $file = $request->file('img_photo');
                    if(!empty($file)) {
                        $sid_photo = Uuid::generate()->string;
                        $file_name ='photo_'.$sid_photo;
                        $file_ext = $file->extension();
                        $file_path = $file_name. '.'.$file_ext;

                        // Backup file
                        // $tmp_name = $_FILES["img_photo"]["tmp_name"];
                        // basename() may prevent filesystem traversal attacks;
                        // further validation/sanitation of the filename may be appropriate
                        // $file_name_temp = uniqid();
                        // $file_path_temp = public_path().join(DIRECTORY_SEPARATOR, array('', 'image', 'photo', 'tmp', $file_name_temp));
                        
                        try {
                            $validated_data = $request->validate(
                                [
                                'img_photo' => 'mimes:jpg,png,gif,tiff,jpeg,heic|min:1|max:46080',
                                ],
                                ['img_photo.mimes' => 'File type is invalid', 'img_photo.min' => 'File size is under 1KB', 'img_photo.max' => 'File size is over 450MB']
                            );
                        }catch(\Exception $ex_upload){
                            throw $ex_upload;
                        }  
                        // if(move_uploaded_file($tmp_name, $file_path_temp)){
                        //     if(!empty($ex_upload)){
                        //         throw $ex_upload;
                        //     } else {
                        //         $url = \Storage::putFileAs('photo', new File($file_path_temp), $file_path);
                        //         $data['img_photo'] = env('APP_URL').'image/'.$url;  
                        //     }
                        // }
                        
                        $url = \Storage::putFileAs('photo',$file,$file_name. '.'.$file->extension());
                        $data['img_photo'] = env('APP_URL').'image/'.$url;
                    }else {
                        $data['img_photo'] = NULL;
                    }
                    if(!empty($data['img_photo'])){
                        $data['created_by'] =  $auth->username;
                        $data['is_deleted'] = 0;
                        $data['is_active'] = 1;
                        $photo = Photo::create($data);
                        if(!empty($photo)){
                            $photo_tags_sid = $data_json['photo_tags_sid'];
                            if(!empty($photo_tags_sid)){
                                foreach($photo_tags_sid as $photo_tag_sid){
                                    DB::table('photo_job_tag')->insert([
                                        'photo_sid' => $sid,
                                        'photo_tag_sid' => $photo_tag_sid,
                                    ]);
                                }
                            }

                            $return_data = response()->json([
                                'status' => "Success",
                                'message' => Exception::CREATED_PHOTO_SUCCESS,
                                //'data' => new PhotoResource($photo),
                                'data' => $data,
                            ],Response::HTTP_CREATED);
                        }
                        DB::commit();
                    }
                } else {
                    $return_data = response()->json(
                        Exception::noPlanActual(),500);
                }
            } else {
                $return_data = response()->json(
                    Exception::dataJsonInvalid(),500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $errors = null;
            if($e instanceof \Illuminate\Validation\ValidationException){
                $errors = $e->errors();
            }
            $message = $e->getMessage();
            if(!empty($data_json['name'])){
                $data_message_arr = array(
                //     ' Details: ', // Space
                );
                // if(!empty($errors['img_photo'])){
                //     $data_error = $errors['img_photo'];
                //     $data_message_arr = array_merge($data_message_arr,$data_error);
                // }

                $data_message_arr[] = ' Please try again for file';

                if(!empty($data_json['name'])){
                    $data_message_arr[] = $data_json['name'];
                }
                // if(!empty($data_json['elevation_code'])){
                //     $data_message_arr[] = $data_json['elevation_code'];
                // }
                // if(!empty($data_json['description'])){
                //     $data_message_arr[] = $data_json['description'];
                // }
                
                $data_message = implode(' ', $data_message_arr);
                $message .= $data_message;
            }
            $return_data = response()->json([
                'status' => 'Failed',
                // 'message' => 'DB error',
                'message' => $message,
                'errors' => $errors,

            ],500);
        }

        debug_log_set_path('debug_log_store_photo_mobile.txt');
        $log_arr = array(
            'location' => __FILE__,
            'function' => 'store_photo_mobile',
            '_POST' => !empty($input_data_old) ? $input_data_old : '',
            'file_name' => !empty($file_name) ? $file_name : '',
            'file_ext' => !empty($file_ext) ? $file_ext : '',
            'tmp_name' => !empty($tmp_name) ? $tmp_name : '',
            'file_path_temp' => !empty($file_path_temp) ? $file_path_temp : '',
            'data' => !empty($data) ? $data : '',
            'validated_data' => !empty($validated_data) ? $validated_data : '',
            'return_data' => !empty($return_data) ? $return_data : '',
        );
        debug_log_from_config($log_arr);
        
        return $return_data;
    }


 /**
    * @SWG\Put(
    *         path="/api/photos/{sid}",
    *         tags={"ADMIN/PHOTO"},
    *         summary="Create a Photo",
    *         @SWG\Parameter(
    *               name="data",
    *               in="body",
    *               default="{}",
    *               required=true,
    *               type="object",
    *               @SWG\Schema(
   *                   @SWG\Property(property="name", type="string", example="string"),
    *                   @SWG\Property(property="description", type="string", example="string"),
    *                   @SWG\Property(property="company_sid", type="string", example="string"),
    *                   @SWG\Property(property="territory_sid", type="string", example="string"),
    *                   @SWG\Property(property="store_sid", type="string", example="string"),
    *                   @SWG\Property(property="job_sid", type="string", example="string"),
    *                   @SWG\Property(property="elevation_code", type="string", example="string"),
    *                   @SWG\Property(property="test_metrie", type="array",@SWG\Items(type="string")),
    *                   @SWG\Property(property="updated_by", type="string", example="string"),
    *                   @SWG\Property(property="is_active", type="string", example=true)
    *               ),
    *               
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this photo.",
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
        $photo = Photo::whereSid($sid)->first();
       
        $data = $request->all();
        $auth = \Auth::user();
        if($request->is_active == 'true'){
            $data['is_active'] = 1;
        }else{
            $data['is_active'] = 0;
        }
        $data['elevation_code'] = $request->elevation_code;
        $employee_username = $request->updated_by;
        if(!empty($employee_username)){
            $employee_name = Employee::whereUsername($employee_username)->first();
            $data['updated_by'] = $employee_name->username;
            
        }else{
            $data['updated_by'] = $auth->username;
        }
        $photo_tags = $request->test_metrie;
        
        $plan = Plan::whereStoreSid($request->store_sid)->whereTerritorySid($request->territory_sid)->whereCompanySid($request->company_sid)->whereJobSid($request->job_sid)->whereStatus('DONE')->first();
        if(!empty($plan)){
            $plan_actual = Plan_actual::wherePlanSid($plan->sid)->first();
            $data['plan_actual_sid'] = $plan_actual->sid;
            $photo_tag = Photo_job_tag::wherePhotoSid($photo->sid);
            $photo_tag->delete();
            foreach($photo_tags as $photo_tag) {
                DB::table('photo_job_tag')->insert([
                    'photo_sid' => $sid,
                    'photo_tag_sid' => $photo_tag,
                ]);
            }
            $photo->update($data);
            return response()->json([
                'status' => "Success",
                'message' => Exception::CREATED_PHOTO_SUCCESS,
                'data' => $photo
            ],Response::HTTP_CREATED);
        }else{
            return response()->json(
                Exception::createdPhotoFailed(),400);
        }
    }

    /**
    * @SWG\Post(
    *         path="/api/photos/{sid}/upload-photo",
    *         tags={"ADMIN/PHOTO"},
    *         description="Upload photo",
    *         consumes={"multipart/form-data"},
    *         produces={"application/json"},
    *         @SWG\Parameter(
    *             name="img_photo",
    *             description="File img photo",
    *             in="formData",
    *             type="file",
    *         ),
    *         @SWG\Parameter(
    *             name="sid",
    *             description="A UUID string identifying this photo.",
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
    public function upload_photo(Request $request,$sid) {
        $photo = Photo::whereSid($sid)->first();
        $file = $request->file('img_photo');
        $sid_photo  = Uuid::generate()->string;
        if(!empty($file)) {
            $request->validate([
                'img_photo' => 'mimes:jpg,png,gif,tiff,jpeg,heic|max:46080',
            ]);
            $name ='photo-'.$sid_photo;
            $url = \Storage::putFileAs('photo',$file,$name. '.'.$file->extension());
            $auth=\Auth::user();
            $photo['updated_by'] = $auth->username;
            $photo->update(['img_photo' => env('APP_URL').'image/'.$url]);
            return response()->json([
                'status' => 'Success',
                'message' => Exception::UPDATED_PHOTO_SUCCESS,
                'image_url' => env('APP_URL').'image/'.$url
            ],Response::HTTP_OK);
        }else {
            return response()->json(
                Exception::uploadedPhotoFileFailed(),400);
        }
        
    }

    /**
    * @SWG\Post(
    *         path="/api/photos/download",
    *         tags={"ADMIN/PHOTO"},
    *         description="Download multiple photos",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               @SWG\Property(property="sid", type="array",@SWG\Items(type="string")),
    *          ),
    *               
    *      ),
    *         @SWG\Response(
    *           response=200,
    *           description="Download url"
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
    public function download_photo(Request $request) {
        $return_data = response()->json(
            Exception::dataInvalid(),400);

        ini_set('memory_limit', '7168M');
        $memory_limit_check = ini_get('memory_limit');
        // die($memory_limit_check);

        $data = $request->data;
        // $job_sid = $request->job_sid; // TODO: Not used
        $plan_sid = $request->plan_sid;
        if(!empty($data['sid'])){
            $sid_arr = $data['sid'];
            $sid_arr_2 = array();
            // var_dump($sid_arr); die();
            foreach($sid_arr as $sid){
                $sid_arr_2[] = "'{$sid}'";
            }
            $sid_str = implode(',',$sid_arr_2);
            // die($sid_str);

            $query = "
                SELECT stores.name AS store_name, photo_elevations.name AS elevation_name, photo.name, photo.img_photo
                FROM plans
                    INNER JOIN stores ON plans.store_sid = stores.sid
                    INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid
                    INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid
                    INNER JOIN photo_elevations ON photo.elevation_code = photo_elevations.code
                WHERE photo.sid IN ({$sid_str})
            ";
            // var_dump($query); die();
            $obj_list = DB::select($query);
        }
        else if(!empty($job_sid)){
            $query = "
                SELECT stores.name AS store_name, photo_elevations.name AS elevation_name, photo.name, photo.img_photo
                FROM plans
                    INNER JOIN stores ON plans.store_sid = stores.sid
                    INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid
                    INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid
                    INNER JOIN photo_elevations ON photo.elevation_code = photo_elevations.code
                WHERE plans.job_sid = '{$job_sid}'
            ";
            // var_dump($query); die();
            $obj_list = DB::select($query);
        }
        else if(!empty($plan_sid)){
            $query = "
                SELECT stores.name AS store_name, photo_elevations.name AS elevation_name, photo.name, photo.img_photo
                FROM plans
                    INNER JOIN stores ON plans.store_sid = stores.sid
                    INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid
                    INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid
                    INNER JOIN photo_elevations ON photo.elevation_code = photo_elevations.code
                WHERE plans.sid = '{$plan_sid}'
            ";
            // var_dump($query); die();
            $obj_list = DB::select($query);
        }
        if(!empty($obj_list)){        
            $base_url = env('APP_URL');
            $download_name = uniqid();
            $download_dir = public_path()."/image/download/{$download_name}/";
            $download_filepath = public_path()."/image/download/{$download_name}.zip";
            $url = $base_url."image/download/{$download_name}.zip";
            @mkdir($download_dir, 0777);
            $flag_has_file = false;
            foreach($obj_list as $obj){
                $img_photo = $obj->img_photo;
                $photo_relative_file_path = $img_photo;
                $photo_relative_file_path = str_replace('https://api.teracom.me/','',$photo_relative_file_path);
                $photo_relative_file_path = str_replace($base_url,'',$photo_relative_file_path);
                $photo_file_path = public_path().'/'.$photo_relative_file_path;

                if(file_exists($photo_file_path)){
                    // die('file_exists '.$photo_file_path);
                    $store_name = $obj->store_name;
                    $elevation_name = $obj->elevation_name;
                    $name = $obj->name;
                    $photo_name_new = "{$store_name}-{$elevation_name}-{$name}";
                    $photo_name_new = SEO($photo_name_new);
                    $extension = '';
                    $path_parts = pathinfo($photo_relative_file_path);
                    if(!empty($path_parts['extension'])){
                        $extension = '.'.$path_parts['extension'];
                    }
                    $photo_name_new = $photo_name_new.$extension;
                    $photo_file_path_new = $download_dir.$photo_name_new;
                    // die($photo_file_path_new);


                    if(copy($photo_file_path, $photo_file_path_new)){
                        // continue
                        $flag_has_file = true;
                        // break; // TODO  - DEBUG
                    }

                }
            }
            if($flag_has_file){
                // die($download_filepath);
                $zip = new \ZipArchive();
                $rootPath = $download_dir;
                $zip->open($download_filepath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                // Create recursive directory iterator
                /** @var SplFileInfo[] $files */
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($rootPath),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file)
                {
                    // Skip directories (they would be added automatically)
                    if (!$file->isDir())
                    {
                        // Get real and relative path for current file
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($rootPath) + 1);

                        // Add current file to archive
                        $zip->addFile($filePath, $relativePath);
                    }
                }

                // Zip archive will be created only after closing object
                $zip->close();
            }

            $result = $url;
            $return_data = response()->json([
                'status' => 'Success',
                'message' => '',
                'result' => $result,
            ],Response::HTTP_OK);
        }
        return $return_data;
    }

    /**
    * @SWG\Delete(
    *         path="/api/photos",
    *         tags={"ADMIN/PHOTO"},
    *         summary="Delete a photo by UUID",
    *      @SWG\Parameter(
    *          name="data",
    *          in="body",
    *          default="{}",
    *          required=true,
    *          type="object",
    *          @SWG\Schema(
    *               @SWG\Property(property="sid", type="array",@SWG\Items(type="string")),
    *          ),
    *               
    *      ),
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
    public function destroy(Request $request)
    {
        $sid = $request->sid;
        foreach($sid as $item) {
            DB::delete('delete from photo WHERE photo.sid = "' .$item.'"');      
        }
        return response()->json([
            'status' => 'Success',
            'message' => Exception::DELETE_PHOTO_SUCCESS,
        ],Response::HTTP_OK);
    }

    
}
