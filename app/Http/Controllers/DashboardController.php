<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Exceptions\Exception;

class DashboardController extends Controller
{
    public function territory(Request $request){
        $job_sid = $request->job_sid;
        $condition = '';
        if(!empty($job_sid)){
          $condition = "WHERE plans.sid IS NULL OR plans.job_sid = '{$job_sid}'";
        }
        $sql = "
          SELECT territories.sid, territories.name, plans.sid AS plan_sid, plans.status
          FROM territories
              LEFT JOIN plans ON territories.sid = plans.territory_sid
          {$condition}
        ";

        $obj_list = DB::select($sql);
        // return $obj_list;
        $obj_mapping = array();
        foreach($obj_list as $obj){
            $obj_sid = $obj->sid;
            $status = $obj->status;
            if(empty($obj_mapping[$obj_sid])){
                $name = $obj->name;
                $obj_mapping[$obj_sid] = array(
                  'name' => $name,
                  'complete_count' => 0,
                  'incomplete_count' => 0,
                );
            }
            if($status != 'INACTIVE'){
              if($status == 'DONE'){
                $obj_mapping[$obj_sid]['complete_count']++;
              } else {  
                $obj_mapping[$obj_sid]['incomplete_count']++;
              }
            } else {
              // Not count
            }
        }

        return response()->json([
            'status' => "Success",
            'data' => array_values ($obj_mapping)
        ],Response::HTTP_OK);
    }

    // public function month(Request $request){
    //     $job_sid = $request->job_sid;
    //     $obj_list = DB::select("
    //             SELECT territories.sid, territories.name, plans.sid AS plan_sid, plans.status
    //             FROM territories
    //                 LEFT JOIN plans ON territories.sid = plans.territory_sid
    //             WHERE plans.sid IS NULL OR plans.job_sid = '{$job_sid}'
    //         ");
    //     // return $obj_list;
    //     $obj_mapping = array();
    //     foreach($obj_list as $obj){
    //         $obj_sid = $obj->sid;
    //         $status = $obj->status;
    //         if(empty($obj_mapping[$obj_sid])){
    //             $name = $obj->name;
    //             $obj_mapping[$obj_sid] = array(
    //               'name' => $name,
    //               'complete_count' => 0,
    //               'incomplete_count' => 0,
    //             );
    //         }
    //         if($status != 'INACTIVE'){
    //           if($status == 'DONE'){
    //             $obj_mapping[$obj_sid]['complete_count']++;
    //           } else {  
    //             $obj_mapping[$obj_sid]['incomplete_count']++;
    //           }
    //         } else {
    //           // Not count
    //         }
    //     }

    //     return response()->json([
    //         'status' => "Success",
    //         'data' => array_values ($obj_mapping)
    //     ],Response::HTTP_OK);
    // }

    public function store(Request $request){
        $job_sid = $request->job_sid;
        $territory_sid = $request->territory_sid;
        $condition = '';
        if(!empty($job_sid)){
          $condition = "plans.job_sid = '{$job_sid}'";
          if(!empty($territory_sid)){
            $condition .= " AND plans.territory_sid = '{$territory_sid}'";
          }
        }
        if(!empty($condition)){
          $condition = "WHERE {$condition}";
        }
        
        $obj_list = DB::select("
                SELECT plans.sid, plan_data.data
                FROM plans
                  INNER JOIN stores ON plans.store_sid = stores.sid			
                  INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid
                  INNER JOIN plan_data ON plan_actual.sid = plan_data.plan_actual_sid
                {$condition}
            ");
        // return $obj_list;
        $obj_mapping = array(
          1 => 0,
          2 => 0,
          3 => 0,
          4 => 0,
          5 => 0,
        );
        foreach($obj_list as $obj){
            $obj_sid = $obj->sid;
            $json_content = $obj->data;
            try {
              $json_data = json_decode($json_content, true);
              if(!empty($json_data['COVER_LETTER_ISS']['data'])){
                $cover_data = $json_data['COVER_LETTER_ISS']['data'];
                $cover_letter_grade = !empty($cover_data['overall_subjective_grade']) ? $cover_data['overall_subjective_grade'] : '';
                $cover_letter_grade = (int)$cover_letter_grade;
              }

              if(isset($obj_mapping[$cover_letter_grade])){
                $obj_mapping[$cover_letter_grade]++;
              }
            } catch (\Exception $e) {
              // $debug_data = array('exception'=>utf8_encode($e->getMessage()));
              // $debug_data['json_data_fail'] = $json_data;
              // echo json_encode($debug_data); die();
              continue;
            }
        }

        return response()->json([
            'status' => "Success",
            'data' => array_values ($obj_mapping)
        ],Response::HTTP_OK);
    }
}
