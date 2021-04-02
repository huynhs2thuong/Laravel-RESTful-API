<?php

namespace App\Exports;

use App\Employee;
use App\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Files\LocalTemporaryFile;
use Maatwebsite\Excel\Sheet;
use phpDocumentor\Reflection\Types\Null_;
use PhpOffice\PhpSSpreadsheet\Style\Border;

class ReportTemplate implements FromArray, Responsable, WithEvents, ShouldAutoSize
{
    use Exportable;
    use RegistersEventListeners;

    private $fileName = "export_file.slsx";
    private $params;
    private $obj_mapping;
	private $elevation_arr;
	private $test_arr;
	private $test_elevation_arr;
	private $stonebridge_test_arr_other;
	private $header;
	private $header_top;
	private $row_dynamic;

    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return Employee::all();
    // }

    function __construct($params) {
        $this->params = $params;
    }
	
	private function mapping_build_data_basic(){
		
	}
	
	private function mapping_build_data_plan($condition){
		$sql = "
            SELECT 
				plans.sid AS plan_sid,
				plan_actual.actual_date,
				plan_data.data
            FROM 
                plans
                INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid
                INNER JOIN plan_data ON plan_actual.sid = plan_data.plan_actual_sid
            WHERE {$condition}
        ";
        // var_dump($sql); die();
		$data_list = DB::select($sql);
		$obj_mapping = $this->obj_mapping;
		$elevation_arr = $this->elevation_arr;
		$test_arr = $this->test_arr;
		$test_elevation_arr = $this->test_elevation_arr;
		foreach($data_list as $data){
            $plan_sid = $data->plan_sid;
			if(!empty($obj_mapping[$plan_sid])){
                $obj = &$obj_mapping[$plan_sid];
				$obj->actual_date = $data->actual_date;
				$json_content = $data->data;
				if(!empty($json_content)){
					try {
						$json_data = json_decode($json_content, true);
						$obj->json_data = $json_data; // TODO - DEBUG
						
						// COVER
						if(!empty($json_data['COVER_LETTER_ISS']['data'])){
							$cover_data = $json_data['COVER_LETTER_ISS']['data'];
							$duration = !empty($cover_data['duration']) ? $cover_data['duration'] : '';
							$obj->duration = $duration;
							$time_of_day = !empty($cover_data['time_of_day']['label']) ? $cover_data['time_of_day']['label'] : '';
							$obj->time_of_day = $time_of_day;
							$weather_condition = !empty($cover_data['weather_condition']['name']) ? $cover_data['weather_condition']['name'] : '';
							$obj->weather_condition = $weather_condition;
							$cover_letter_grade = !empty($cover_data['overall_subjective_grade']) ? $cover_data['overall_subjective_grade'] : '';
							$obj->cover_letter_grade = $cover_letter_grade;
						}
						
						// ELEVATION + TEST METRIC
						$obj->test_mapping = array();
						foreach($elevation_arr as $elevation_code => $elevation_name){
							$issue_code = $elevation_code.'_ISS'; // Different with Cover and Stonebridge
							if(!empty($json_data[$issue_code]['data'])){
								$issue_data = $json_data[$issue_code]['data'];
								foreach($test_arr as $test_code => $test_count){ // Keep order
									if(empty($obj->test_mapping[$test_code][$elevation_code])){
										$obj->test_mapping[$test_code][$elevation_code] = array(
											// 'test_site_count' => 0,
										);
									}
									if(isset($obj->test_mapping[$test_code][$elevation_code])){
										$test_mapping_obj = &$obj->test_mapping[$test_code][$elevation_code];
										
										if(!empty($issue_data[$test_code])){
											$test_data = $issue_data[$test_code];
											if($test_code == 'JOINTS'){
												if(!empty($test_data['joint_list'])){
													$joint_list = $test_data['joint_list'];
													$joint_count = 0;
													$joint_sum = 0;
													foreach($joint_list as $joint_key => $joint_value){
														if(!empty($joint_value)){
															$joint_count++;
															$joint_sum += (int)$joint_value;
														}
													}
													$test_site_count = 1;
													$test_arr[$test_code] = $test_site_count;
													$test_site_number = $test_site_count;
													$avegrade = $joint_count != 0 ? round($joint_sum/$joint_count,1) : 0;
													$test_site_data = array('avegrade' => $avegrade);
													$test_mapping_obj[$test_site_number] = $test_site_data;
												}
											} 
                      // else if($test_code == 'PENETRATION_POINTS'){
												// if(!empty($test_data['count'])){
													// $count = $test_data['count'];
													
													// $test_site_count = 1;
													// $test_arr[$test_code] = $test_site_count;
													// $test_site_number = $test_site_count;
													// $test_site_data = array('count' => $count);
													// $test_mapping_obj[$test_site_number] = $test_site_data;
												// }
											// }
                      else if(in_array($test_code, array('BLISTERING','FLAKING','MUDCRACKING','PINHOLES'))){
												if(!empty($test_data['moderate'])){
													$moderate = $test_data['moderate'];
													$severe = !empty($test_data['severe']) ? $test_data['severe'] : '';
													
													$test_site_count = 1;
													$test_arr[$test_code] = $test_site_count;
													$test_site_number = $test_site_count;
													$test_site_data = array(
														'moderate' => $moderate,
														'severe' => $severe,
													);
													$test_mapping_obj[$test_site_number] = $test_site_data;
												}
											} else if($test_code == 'EFFLORESCENCE'){
												if(!empty($test_data['acreage'])){
													$acreage = $test_data['acreage'];
													
													$test_site_count = 1;
													$test_arr[$test_code] = $test_site_count;
													$test_site_number = $test_site_count;
													$test_site_data = array('acreage' => $acreage);
													$test_mapping_obj[$test_site_number] = $test_site_data;
												}
											} else {
												if(!empty($test_data['test_sites'])){
													$test_sites = $test_data['test_sites'];
													foreach($test_sites as $test_site_key => $test_site_data){
														if(!empty($test_site_data['test_site'])){
															$test_site_number = $test_site_data['test_site'];
															$test_mapping_obj[$test_site_number] = $test_site_data;
															
															// Update test_site_count of test_mapping_obj and test_arr
															$test_site_count = count(array_keys($test_mapping_obj));
															$test_count_total = $test_arr[$test_code];
															if($test_count_total < $test_site_count){
																$test_arr[$test_code] = $test_site_count;
															}
															$test_count_total_elevation = $test_elevation_arr[$test_code][$elevation_code];
															if($test_count_total_elevation < $test_site_count){
																$test_elevation_arr[$test_code][$elevation_code] = $test_site_count;
															}
														}
													}
												}
											}
										}
										
										unset($test_mapping_obj);
									}
									
									
								}
							}
						}
						
						// STONEBRIDGE - Codes must be put here - after processing other test data
						if(!empty($json_data['STONEBRIDGE_ISS']['data'])){
							$stonebridge_data = $json_data['STONEBRIDGE_ISS']['data'];
							$test_code = 'STONEBRIDGE';
							foreach($elevation_arr as $elevation_code => $elevation_name){ // Keep order of elevation
								if(!empty($stonebridge_data[$elevation_code])){
									$stonebridge_value = $stonebridge_data[$elevation_code];
									if(!empty($stonebridge_value['is_enable'])){
										foreach($this->stonebridge_test_arr_other as $test_code_other){
											$test_mapping_obj_other = $obj->test_mapping[$test_code_other][$elevation_code];
											if(!is_array($test_elevation_arr[$test_code][$elevation_code])){
												$test_elevation_arr[$test_code][$elevation_code] = array(); // Fix erro - Exception: "Cannot use a scalar value as an array"
											}
											if(!isset($test_elevation_arr[$test_code][$elevation_code][$test_code_other])){
												$test_elevation_arr[$test_code][$elevation_code][$test_code_other] = 0; 
											}
											$test_site_count_other = count(array_keys($test_mapping_obj_other));
											if(!empty($test_site_count_other)){
												$test_count_total_elevation_other = $test_elevation_arr[$test_code][$elevation_code][$test_code_other];
												if($test_count_total_elevation_other < $test_site_count_other){
													$test_elevation_arr[$test_code][$elevation_code][$test_code_other] = $test_site_count_other;
												}
											}
										}

										// $test_mapping_obj = &$obj->test_mapping[$test_code][$elevation_code];
										$obj->test_mapping[$test_code][$elevation_code][0] = 1; // Mark is_enable
									}
								}
							}
						} else {
							// TODO - DEBUG
							// $debug_data['json_data_fail'] = $json_data;
							// echo json_encode($debug_data); die();
						}							
						// STONEBRIDGE_ISS
					} catch (\Exception $e) {
						$debug_data = array('exception'=>utf8_encode($e->getMessage()));
						$debug_data['json_data_fail'] = $json_data;
						echo json_encode($debug_data); die();
						continue;
					}
					
				}
				unset($obj);
            }
		}
		$this->test_arr = $test_arr;
		$this->test_elevation_arr = $test_elevation_arr;
		$this->obj_mapping = $obj_mapping;
		
		// TODO - DEBUG
		// $debug_data['test_elevation_arr'] = $this->test_elevation_arr;
		// $debug_data['test_arr'] = $this->test_arr;
		// $debug_data['obj_mapping'] = $this->obj_mapping;
		// echo json_encode($debug_data); die();
	}
	
	private function mapping_build_data_photo($condition){
		$sql = "
            SELECT 
				plans.sid AS plan_sid, 
				photo.id, photo.sid, photo.img_photo, photo.elevation_code, photo.name, photo.description,
				photo_job_tag.photo_tag_sid
            FROM 
                plans 
                INNER JOIN plan_actual ON plans.sid = plan_actual.plan_sid 
                INNER JOIN photo ON plan_actual.sid = photo.plan_actual_sid 
                INNER JOIN photo_job_tag ON photo.sid = photo_job_tag.photo_sid 
            WHERE {$condition}
        ";
        // var_dump($sql); die();
		$data_list = DB::select($sql);
		$obj_mapping = $this->obj_mapping;
		
		foreach($data_list as $data){
            $plan_sid = $data->plan_sid;
			if(!empty($obj_mapping[$plan_sid])){
                $obj = &$obj_mapping[$plan_sid];
				// TODO
				unset($obj);
            }
		}
		
		$this->obj_mapping = $obj_mapping;
	}

	private function excel_build_header(){
		$row_top = array();
		$row = $this->header;
		for($i=0;$i < count($row);$i++){
			$row_top[] = '';
		}
		$row_top[0] = 'CATEGORY';
		$row_top[2] = 'COVER LETTER';
		$row_dynamic = $this->row_dynamic;
		
		$elevation_arr = $this->elevation_arr;
		$test_elevation_arr = $this->test_elevation_arr;
		$test_arr = $this->test_arr;
		foreach($test_elevation_arr as $test_code => $elevation_data){
			$test_count = !empty($test_arr[$test_code]) ? $test_arr[$test_code] : 0;
			if($test_code == 'PAINT_THICKNESS'){
				$row_top_cell_start = false;
				if($test_count){
					$row_top[] = 'PAINT THICKNESS';
					$row_top_cell_start = true;
				}
				foreach($elevation_data as $elevation_code => $elevation_count){
					$elevation_name = $elevation_arr[$elevation_code];
					for($test_site_number = 1; $test_site_number <= $elevation_count; $test_site_number++){ // FRONT ELEVATION TEST MATERIAL | FRONT ELEVATION TEST RESULT | ...
						$number = $elevation_count > 0 ? $test_site_number : '';
						if(empty($row_top_cell_start)){
							$row_top[] = '';
						} else {
							$row_top_cell_start = false;
						}
						$row[] = "{$elevation_name} ELEVATION #{$number} TEST MATERIAL"; // TODO - Spacing error
						$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_MATERIAL");
						$row_dynamic[$key] = '';
						
						$row_top[] = '';
						$row[] = "{$elevation_name} ELEVATION #{$number} TEST RESULT";
						$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_RESULT");
						$row_dynamic[$key] = '';
					}
				}  
			}
			else if($test_code == 'RILEM'){
				$row_top_cell_start = false;
				if($test_count){
					$row_top[] = 'RILEM TEST LOCATION MATERIAL';
					$row_top_cell_start = true;
				}
				foreach($elevation_data as $elevation_code => $elevation_count){
					$elevation_name = $elevation_arr[$elevation_code];
					for($test_site_number = 1; $test_site_number <= $elevation_count; $test_site_number++){ // FRONT #1 TEST MATERIAL | FRONT #2 TEST MATERIAL | ...
						$number = $elevation_count > 0 ? $test_site_number : '';
						if(empty($row_top_cell_start)){
							$row_top[] = '';
						} else {
							$row_top_cell_start = false;
						}
						$row[] = "{$elevation_name} #{$number} TEST MATERIAL";
						$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_MATERIAL");
						$row_dynamic[$key] = '';
					}
				}
				
				$row_top_cell_start = false;
				if($test_count){
					$row_top[] = 'RILEM REMAINING WATER AFTER 30 MINUTES';
					$row_top_cell_start = true;
				}
				foreach($elevation_data as $elevation_code => $elevation_count){
					$elevation_name = $elevation_arr[$elevation_code];
					for($test_site_number = 1; $test_site_number <= $elevation_count; $test_site_number++){ // FRONT #1 REMAINING WATER AFTER 30 MINUTES (ML) | FRONT #2 REMAINING WATER AFTER 30 MINUTES (ML) | ...
						$number = $elevation_count > 0 ? $test_site_number : '';
						if(empty($row_top_cell_start)){
							$row_top[] = '';
						} else {
							$row_top_cell_start = false;
						}
						$row[] = "{$elevation_name} #{$number} REMAINING WATER AFTER 30 MINUTES (ML)";
						$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_REMAINING");
						$row_dynamic[$key] = '';
					}
				}
				
				$row_top_cell_start = false;
				if($test_count){
					$row_top[] = 'RILEM GRADE';
					$row_top_cell_start = true;
				}
				foreach($elevation_data as $elevation_code => $elevation_count){
					$elevation_name = $elevation_arr[$elevation_code];
					for($test_site_number = 1; $test_site_number <= $elevation_count; $test_site_number++){ // FRONT #1 GRADE | FRONT #2 GRADE
						$number = $elevation_count > 0 ? $test_site_number : '';
						if(empty($row_top_cell_start)){
							$row_top[] = '';
						} else {
							$row_top_cell_start = false;
						}
						$row[] = "{$elevation_name} #{$number} GRADE";
						$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_GRADE");
						$row_dynamic[$key] = '';
					}
				}
			} 
			else if($test_code == 'ADHESION'){
				$row_top_cell_start = false;
				if($test_count){
					$row_top[] = 'ADHESION TEST MATERIAL';
					$row_top_cell_start = true;
				}
				
				foreach($elevation_data as $elevation_code => $elevation_count){
					$elevation_name = $elevation_arr[$elevation_code];
					for($test_site_number = 1; $test_site_number <= $elevation_count; $test_site_number++){ // FRONT #1 TEST MATERIAL | FRONT #2 TEST MATERIAL | ...
						// $row_top[] = $test_site_number; // TODO - DEBUG
						$number = $elevation_count > 0 ? $test_site_number : '';
						if(empty($row_top_cell_start)){
							$row_top[] = '';
						} else {
							$row_top_cell_start = false;
						}
						$row[] = "{$elevation_name} #{$number} TEST MATERIAL";
						$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_MATERIAL");
						$row_dynamic[$key] = '';
					}
				}
				
				$row_top_cell_start = false;
				if($test_count){
					$row_top[] = 'ADHESION TEST GRADE';
					$row_top_cell_start = true;
				}
				foreach($elevation_data as $elevation_code => $elevation_count){
					$elevation_name = $elevation_arr[$elevation_code];
					for($test_site_number = 1; $test_site_number <= $elevation_count; $test_site_number++){ // FRONT #1 GRADE | FRONT #2 GRADE
						$number = $elevation_count > 0 ? $test_site_number : '';
						if(empty($row_top_cell_start)){
							$row_top[] = '';
						} else {
							$row_top_cell_start = false;
						}
						$row[] = "{$elevation_name} #{$number} GRADE";
						$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_GRADE");
						$row_dynamic[$key] = '';
					}
				}
			}
			else if($test_code == 'JOINTS'){
				$row_top_cell_start = false;
				if($test_count){
					$row_top[] = 'JOINT AVERAGE GRADE';
					$row_top_cell_start = true;
				}
				foreach($elevation_arr as $elevation_code => $elevation_name){
					// FRONT AVERAGE GRADE | ...
					if(empty($row_top_cell_start)){
						$row_top[] = '';
					} else {
						$row_top_cell_start = false;
					}
					$row[] = "{$elevation_name} AVERAGE GRADE";
					$key = strtolower("{$test_code}_{$elevation_code}_GRADE");
					$row_dynamic[$key] = '';
				}
			}
			// else if($test_code == 'PENETRATION_POINTS'){
			// 	$row_top_cell_start = false;
			// 	if($test_count){
			// 		$row_top[] = 'PENETRATION POINTS';
			// 		$row_top_cell_start = true;
			// 	}
			// 	foreach($elevation_arr as $elevation_code => $elevation_name){
			// 		// FRONT COUNT | ...
			// 		if(empty($row_top_cell_start)){
			// 			$row_top[] = '';
			// 		} else {
			// 			$row_top_cell_start = false;
			// 		}
			// 		$row[] = "{$elevation_name} COUNT";
			// 		$key = strtolower("{$test_code}_{$elevation_code}_COUNT");
			// 		$row_dynamic[$key] = '';
			// 	}
			// }
			else if($test_code == 'MOISTURE_CONTENT'){
				$row_top_cell_start = false;
				if($test_count){
					$row_top[] = 'MOISTURE CONTENT READING';
					$row_top_cell_start = true;
				}
				foreach($elevation_data as $elevation_code => $elevation_count){
					$elevation_name = $elevation_arr[$elevation_code];
					for($test_site_number = 1; $test_site_number <= $elevation_count; $test_site_number++){ // FRONT ELEVATION MATERIAL | FRONT ELEVATION RESULT | ...
						$number = $elevation_count > 0 ? $test_site_number : '';
						if(empty($row_top_cell_start)){
							$row_top[] = '';
						} else {
							$row_top_cell_start = false;
						}
						$row[] = "{$elevation_name} ELEVATION #{$number} MATERIAL"; // TODO - Spacing error
						$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_MATERIAL");
						$row_dynamic[$key] = '';
						
					}
				}
				foreach($elevation_data as $elevation_code => $elevation_count){
					$elevation_name = $elevation_arr[$elevation_code];
					for($test_site_number = 1; $test_site_number <= $elevation_count; $test_site_number++){ // FRONT ELEVATION MATERIAL | FRONT ELEVATION RESULT | ...
						$number = $elevation_count > 0 ? $test_site_number : '';
						$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_RESULT");
						$row_top[] = '';
						$row[] = "{$elevation_name} ELEVATION #{$number} RESULT";
						$row_dynamic[$key] = '';
					}
				}
			}
			else if($test_code == 'BLISTERING'){
				$row_top[] = 'BLISTERING SQUARE FOOTAGE (BY SEVERITY LEVEL)';
				$row_top_cell_start = true;
				foreach($elevation_arr as $elevation_code => $elevation_name){
					// BLISTERING FRONT MODERATE | BLISTERING FRONT SEVERE | ...
					if(empty($row_top_cell_start)){
						$row_top[] = '';
					} else {
						$row_top_cell_start = false;
					}
					$row[] = "BLISTERING {$elevation_name} MODERATE";
					$key = strtolower("{$test_code}_{$elevation_code}_MODERATE");
					$row_dynamic[$key] = '';
					
					$row_top[] = '';
					$row[] = "BLISTERING {$elevation_name} FRONT SEVERE";
					$key = strtolower("{$test_code}_{$elevation_code}_SEVERE");
					$row_dynamic[$key] = '';
				}
			}
			else if($test_code == 'FLAKING'){
				$row_top[] = 'FLAKING SQUARE FOOTAGE (BY SEVERITY LEVEL)';
				$row_top_cell_start = true;
				foreach($elevation_arr as $elevation_code => $elevation_name){
					// FLAKING FRONT MODERATE | FLAKING FRONT SEVERE | ...
					if(empty($row_top_cell_start)){
						$row_top[] = '';
					} else {
						$row_top_cell_start = false;
					}
					$row[] = "FLAKING {$elevation_name} MODERATE";
					$key = strtolower("{$test_code}_{$elevation_code}_MODERATE");
					$row_dynamic[$key] = '';
					
					$row_top[] = '';
					$row[] = "FLAKING {$elevation_name} SEVERE";
					$key = strtolower("{$test_code}_{$elevation_code}_SEVERE");
					$row_dynamic[$key] = '';
				}
			}
			else if($test_code == 'MUDCRACKING'){
				$row_top[] = 'MUDCRACKING SQUARE FOOTAGE (BY SEVERITY LEVEL)';
				$row_top_cell_start = true;
				foreach($elevation_arr as $elevation_code => $elevation_name){
					// FLAKING FRONT MODERATE | FLAKING FRONT SEVERE | ...
					if(empty($row_top_cell_start)){
						$row_top[] = '';
					} else {
						$row_top_cell_start = false;
					}
					$row[] = "MUDCRACKING {$elevation_name} MODERATE";
					$key = strtolower("{$test_code}_{$elevation_code}_MODERATE");
					$row_dynamic[$key] = '';
					
					$row_top[] = '';
					$row[] = "MUDCRACKING {$elevation_name} SEVERE";
					$key = strtolower("{$test_code}_{$elevation_code}_SEVERE");
					$row_dynamic[$key] = '';
				}
			}
			else if($test_code == 'PINHOLES'){
				$row_top[] = 'PINHOLES SQUARE FOOTAGE (BY SEVERITY LEVEL)';
				$row_top_cell_start = true;
				foreach($elevation_arr as $elevation_code => $elevation_name){
					// PINHOLES FRONT MODERATE | MUDCRACKING FRONT SEVERE | ...
					if(empty($row_top_cell_start)){
						$row_top[] = '';
					} else {
						$row_top_cell_start = false;
					}
					
					$row[] = "PINHOLES {$elevation_name} MODERATE";
					$key = strtolower("{$test_code}_{$elevation_code}_MODERATE");
					$row_dynamic[$key] = '';
					
					$row_top[] = '';
					$row[] = "PINHOLES {$elevation_name} SEVERE";
					$key = strtolower("{$test_code}_{$elevation_code}_SEVERE");
					$row_dynamic[$key] = '';
				}
			}
			else if($test_code == 'EFFLORESCENCE'){
				$row_top[] = 'EFFLORESCENCE SQUARE FOOTAGE (BY SEVERITY LEVEL & V/H)';
				$row_top_cell_start = true;
				foreach($elevation_arr as $elevation_code => $elevation_name){
					// FRONT ELEVATION V/H OR "N/A"
					if(empty($row_top_cell_start)){
						$row_top[] = '';
					} else {
						$row_top_cell_start = false;
					}
					$row[] = "{$elevation_name} ELEVATION V/H OR 'N/A'";
					$key = strtolower("{$test_code}_{$elevation_code}_ELEVATION");
					$row_dynamic[$key] = '';
				}
			}
			else if($test_code == 'STONEBRIDGE'){
				$row_top_cell_start = false;
				// if($test_count){ // test_count is default = 0
					$row_top[] = 'STONEBRIDGE LABS';
					$row_top_cell_start = true;
				// }
				$test_elevation_count_arr = $test_elevation_arr[$test_code];
				$number = 1;
				
				
				foreach($elevation_arr as $elevation_code => $elevation_name){  // Keep order
					foreach($this->stonebridge_test_arr_other as $test_code_other){ // Keep order
						if(!empty($test_elevation_count_arr[$elevation_code][$test_code_other])){
							$elevation_count = $test_elevation_count_arr[$elevation_code][$test_code_other];
							for($test_site_number = 1; $test_site_number <= $elevation_count; $test_site_number++){
								// SAMPLE #1 ELEVATION | SAMPLE #1 LOCATION MATERIAL | SAMPLE #1 MEASUREMENT
								// $number = $elevation_count > 0 ? $test_site_number : ''; // Wrong codes for all???
								if(empty($row_top_cell_start)){
									$row_top[] = '';
								} else {
									$row_top_cell_start = false;
								}
								$row[] = "SAMPLE #{$number} ELEVATION {$test_code_other}_{$elevation_code}";
								$key = strtolower("{$test_code}_{$test_code_other}_{$elevation_code}_{$test_site_number}_ELEVATION");
								$row_dynamic[$key] = '';
								
								$row_top[] = '';
								$row[] = "SAMPLE #{$number} LOCATION MATERIAL {$test_code_other}_{$elevation_code}";
								$key = strtolower("{$test_code}_{$test_code_other}_{$elevation_code}_{$test_site_number}_MATERIAL");
								$row_dynamic[$key] = '';
								
								$row_top[] = '';
								$row[] = "SAMPLE #{$number} MEASUREMENT {$test_code_other}_{$elevation_code}";
								$key = strtolower("{$test_code}_{$test_code_other}_{$elevation_code}_{$test_site_number}_MEASUREMENT");
								$row_dynamic[$key] = '';
								
								$number++;
							}
						}
					}
				}
			}
		}
		
		$this->header = $row;
		$this->header_top = $row_top;
		$this->row_dynamic = $row_dynamic;
		
		// TODO - DEBUG
		// $debug_data['elevation_arr'] = $this->elevation_arr;
		// $debug_data['test_elevation_arr'] = $this->test_elevation_arr;
		// $debug_data['header_top'] = $this->header_top;
		// $debug_data['header'] = $this->header;
		// $debug_data['row_dynamic'] = $this->row_dynamic;
		// echo json_encode($debug_data); die();
		
		// return $row;
	}
	
	private function excel_build_test_data(){
		$obj_mapping = $this->obj_mapping;
		$row_dynamic = $this->row_dynamic;
		foreach($obj_mapping as $plan_sid => &$obj){
			$obj->row_test_data = $row_dynamic;
			$row_test_data = &$obj->row_test_data;
			
			$test_mapping = $obj->test_mapping;
			$stonebridge_mapping = array();
			$elevation_arr = $this->elevation_arr;
			$test_elevation_arr = $this->test_elevation_arr;
			foreach($test_mapping as $test_code => $test_mapping_ele_obj){
				if($test_code == 'STONEBRIDGE'){
					continue;
				}
				foreach($test_mapping_ele_obj as $elevation_code => $test_mapping_obj){
					foreach($test_mapping_obj as $test_site_number => $test_site_data){
						if($test_code == 'PAINT_THICKNESS'){
							if(!empty($test_site_data['material'])){
								$material = $test_site_data['material'];
								$measurement = !empty($test_site_data['measurement']) ? $test_site_data['measurement'] : '';
								
								$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_MATERIAL");
								$row_test_data[$key] = $material;
								$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_RESULT");
								$row_test_data[$key] = $measurement;
							}
						}
						if($test_code == 'RILEM'){
							if(!empty($test_site_data['material'])){
								$material = $test_site_data['material'];
								$remaining = !empty($test_site_data['remaining']) ? $test_site_data['remaining'] : '';
								$grade = !empty($test_site_data['grade']) ? $test_site_data['grade'] : '';
								
								// RILEM TEST LOCATION MATERIAL
								$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_MATERIAL");
								$row_test_data[$key] = $material;
								// RILEM REMAINING WATER AFTER 30 MINUTES
								$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_REMAINING");
								$row_test_data[$key] = $remaining;
								// RILEM GRADE
								$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_GRADE");
								// $grade .= '_'.$key; // TODO - DEBUG
								$row_test_data[$key] = $grade;
								
								// stonebridge_mapping
								$test_code_stonebridge = 'STONEBRIDGE';
								if(isset( $test_elevation_arr[$test_code_stonebridge])){
									$test_elevation_count_arr = $test_elevation_arr[$test_code_stonebridge];
									if(!empty($test_elevation_count_arr[$elevation_code][$test_code])){
										$elevation = !empty($elevation_arr[$elevation_code]) ? $elevation_arr[$elevation_code] : $elevation_code;
										$stonebridge_mapping[$test_code][$elevation_code][$test_site_number] = array(
											'elevation' => $elevation,
											'material' => $material,
											'measurement' => $remaining,
										);
									}
								}
							}
						}
						if($test_code == 'ADHESION'){
							if(!empty($test_site_data['material'])){
								$material = $test_site_data['material'];
								$grade = !empty($test_site_data['grade']) ? $test_site_data['grade'] : '';
								
								// ADHESION TEST MATERIAL
								$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_MATERIAL");
								$row_test_data[$key] = $material;
								// ADHESION TEST GRADE
								$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_GRADE");
								$row_test_data[$key] = $grade;
								
								// stonebridge_mapping
								$test_code_stonebridge = 'STONEBRIDGE';
								if(isset( $test_elevation_arr[$test_code_stonebridge])){
									$test_elevation_count_arr = $test_elevation_arr[$test_code_stonebridge];
									if(!empty($test_elevation_count_arr[$elevation_code][$test_code])){
										$elevation = !empty($elevation_arr[$elevation_code]) ? $elevation_arr[$elevation_code] : $elevation_code;
										$stonebridge_mapping[$test_code][$elevation_code][$test_site_number] = array(
											'elevation' => $elevation,
											'material' => $material,
											'measurement' => $grade,
										);
									}
								}
							}
						}
						if($test_code == 'JOINTS'){
							if(!empty($test_site_data['avegrade'])){
								$grade = $test_site_data['avegrade'];
								
								$key = strtolower("{$test_code}_{$elevation_code}_GRADE");
								$row_test_data[$key] = $grade;
							}
						}
						// if($test_code == 'PENETRATION_POINTS'){
							// if(!empty($test_site_data['count'])){
								// $count = $test_site_data['count'];
								
								// $key = strtolower("{$test_code}_{$elevation_code}_COUNT");
								// $row_test_data[$key] = $count;
							// }
						// }
						if($test_code == 'MOISTURE_CONTENT'){
							if(!empty($test_site_data['material'])){
								$material = $test_site_data['material'];
								$measurement = !empty($test_site_data['measurement']) ? $test_site_data['measurement'] : '';
								
								$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_MATERIAL");
								$row_test_data[$key] = $material;
								$key = strtolower("{$test_code}_{$elevation_code}_{$test_site_number}_RESULT");
								$row_test_data[$key] = $measurement;
							}
						}
						if($test_code == 'BLISTERING'){
							if(!empty($test_site_data['moderate'])){
								$moderate = $test_site_data['moderate'];
								$severe = !empty($test_site_data['severe']) ? $test_site_data['severe'] : '';
								
								$key = strtolower("{$test_code}_{$elevation_code}_MODERATE");
								$row_test_data[$key] = $moderate;
								$key = strtolower("{$test_code}_{$elevation_code}_SEVERE");
								$row_test_data[$key] = $severe;
							}
						}
						if($test_code == 'FLAKING'){
							if(!empty($test_site_data['moderate'])){
								$moderate = $test_site_data['moderate'];
								$severe = !empty($test_site_data['severe']) ? $test_site_data['severe'] : '';
								
								$key = strtolower("{$test_code}_{$elevation_code}_MODERATE");
								$row_test_data[$key] = $moderate;
								$key = strtolower("{$test_code}_{$elevation_code}_SEVERE");
								$row_test_data[$key] = $severe;
							}
						}
						if($test_code == 'MUDCRACKING'){
							if(!empty($test_site_data['moderate'])){
								$moderate = $test_site_data['moderate'];
								$severe = !empty($test_site_data['severe']) ? $test_site_data['severe'] : '';
								
								$key = strtolower("{$test_code}_{$elevation_code}_MODERATE");
								$row_test_data[$key] = $moderate;
								$key = strtolower("{$test_code}_{$elevation_code}_SEVERE");
								$row_test_data[$key] = $severe;
							}
						}
						if($test_code == 'PINHOLES'){
							if(!empty($test_site_data['moderate'])){
								$moderate = $test_site_data['moderate'];
								$severe = !empty($test_site_data['severe']) ? $test_site_data['severe'] : '';
								
								$key = strtolower("{$test_code}_{$elevation_code}_MODERATE");
								$row_test_data[$key] = $moderate;
								$key = strtolower("{$test_code}_{$elevation_code}_SEVERE");
								$row_test_data[$key] = $severe;
							}
						}
						if($test_code == 'EFFLORESCENCE'){
							if(!empty($test_site_data['acreage'])){
								$moderate = $test_site_data['acreage'];
								
								$key = strtolower("{$test_code}_{$elevation_code}_ELEVATION");
								$row_test_data[$key] = $moderate;
							}
						}
					}
				}
			}
			
			// echo json_encode($stonebridge_mapping); die();
			// echo json_encode($obj); die();
			// echo json_encode($test_mapping); die();
			
			// 'STONEBRIDGE'
			$test_code = 'STONEBRIDGE';
			foreach($this->stonebridge_test_arr_other as $test_code_other){ // Keep order
				foreach($elevation_arr as $elevation_code => $elevation_name){  // Keep order
					if(!empty($test_mapping[$test_code][$elevation_code][0])){ // Check is_enable
						if(!empty($stonebridge_mapping[$test_code_other][$elevation_code])){
							$stonebridge_data = $stonebridge_mapping[$test_code_other][$elevation_code];
							// echo json_encode($stonebridge_data); die();
							foreach($stonebridge_data as $test_site_number => $test_site_data){
								$key = strtolower("{$test_code}_{$test_code_other}_{$elevation_code}_{$test_site_number}_ELEVATION");
								$row_test_data[$key] = $test_site_data['elevation'];
								
								$key = strtolower("{$test_code}_{$test_code_other}_{$elevation_code}_{$test_site_number}_MATERIAL");
								$row_test_data[$key] = $test_site_data['material'];
								
								$key = strtolower("{$test_code}_{$test_code_other}_{$elevation_code}_{$test_site_number}_MEASUREMENT");
								$row_test_data[$key] = $test_site_data['measurement'];
							}
						}
					}
				}
			}

			// echo json_encode($row_test_data); die();
			
			unset($row_test_data);
			unset($obj);
		}
		$this->obj_mapping = $obj_mapping;
	}
	
    public function array(): array
    {
		$this->elevation_arr = array(
			'FR' => 'FRONT',
			'RT' => 'RIGHT',
			'RE' => 'REAR',
			'LT' => 'LEFT',
		);
		
		$elevation_count_arr = array();
		foreach($this->elevation_arr as $elevation_code => $elevation){
			$elevation_count_arr[$elevation_code] = 0;
		}
		
		$this->test_arr = array(
			'PAINT_THICKNESS' => 0,
			'RILEM' => 0,
			'ADHESION' => 0,
			'JOINTS' => 0,
			// 'PENETRATION_POINTS' => 0,
			'MOISTURE_CONTENT' => 0,
			'BLISTERING' => 0,
			'FLAKING' => 0,
			'MUDCRACKING' => 0,
			'PINHOLES' => 0,
			'EFFLORESCENCE' => 0,
			'STONEBRIDGE' => 0,
		);
		$this->test_elevation_arr = array(
			'PAINT_THICKNESS' => $elevation_count_arr,
			'RILEM' => $elevation_count_arr,
			'ADHESION' => $elevation_count_arr,
			'JOINTS' => $elevation_count_arr,
			// 'PENETRATION_POINTS' => $elevation_count_arr,
			'MOISTURE_CONTENT' => $elevation_count_arr,
			'BLISTERING' => $elevation_count_arr,
			'FLAKING' => $elevation_count_arr,
			'MUDCRACKING' => $elevation_count_arr,
			'PINHOLES' => $elevation_count_arr,
			'EFFLORESCENCE' => $elevation_count_arr,
			'STONEBRIDGE' => $elevation_count_arr,
		);
		$this->stonebridge_test_arr_other = array(
			'ADHESION',
			'RILEM',
		);
		
						
		$this->header = array(
			'STORE #',
			'Overall Subjective Grade (1-5)',
			'Schematics File # (on A2 file)',
			'Date of Schematics (on A2 file)',
			'STORE TYPE',
			'CITY',
			'STATE',
			'EVALUATION DATE',
			'INSPECTOR NAME',
			'EVALUATION DURATION',
			'TIME OF DAY',
			'WEATHER STATUS',
		);
        
        $params = $this->params;
        
		$condition = "plans.status = 'DONE'";
		if(!empty($params['plan_sid'])){
			$plan_sid = $params['plan_sid'];
			$condition .= " AND plans.sid = '{$plan_sid}'";
		}
		else if(!empty($params['job_sid'])){
			$job_sid = $params['job_sid'];
			$condition .= " AND plans.job_sid = '{$job_sid}'";
		} 
		
		$sql = "
            SELECT 
            plans.sid AS plan_sid,
			store_types.name AS store_type_name,
			stores.name AS store_name, stores.a2_file_number, stores.a2_day_on_file, stores.city, stores.state,
			employees.first_name, employees.last_name
            FROM 
                plans 
                INNER JOIN stores ON plans.store_sid = stores.sid
                INNER JOIN store_types ON stores.store_type_sid = store_types.sid
                INNER JOIN employees ON plans.employee_sid = employees.sid
            WHERE {$condition}
        ";
        // var_dump($sql); die();
		$data_list = DB::select($sql);
		$obj_mapping = array();
		foreach($data_list as $data){
            $plan_sid = $data->plan_sid;
            // $obj_tag_sid = $data->obj_tag_sid;
			
			if(empty($obj_mapping[$plan_sid])){
                // $data->obj_tags_sid = array();
                $obj_mapping[$plan_sid] = $data;
            }
            // $obj_mapping[$plan_sid]->obj_tags_sid[] = $obj_tag_sid;
		}
		
		$this->obj_mapping = $obj_mapping;
		$this->mapping_build_data_plan($condition);
		$this->excel_build_header(); // Must called before excel_build_rows, after mapping_build_data_plan
		
		// TODO - DEBUG
		// $debug_data['header_top'] = $this->header_top;
		// $debug_data['header'] = $this->header;
		// echo json_encode($debug_data); die();
		
		$this->excel_build_test_data();
		// $this->mapping_build_data_photo($condition); // NOT USED?
		$obj_mapping = $this->obj_mapping;
		// echo json_encode($obj_mapping); die();

		$result = array();
		
		$result[] = $this->header_top;
		$result[] = $this->header;
		// echo json_encode($result); die();
		// echo json_encode($this->row_dynamic); die();
		
		
		foreach($obj_mapping as $obj){
			$first_name = $obj->first_name;
			$last_name = $obj->last_name;
			$row = array();
			$row[] = $obj->store_name; // STORE #
			$row[] = $obj->cover_letter_grade; // Overall Subjective Grade (1-5)
			$row[] = $obj->a2_file_number; // Schematics File # (on A2 file)
			$row[] = $obj->a2_day_on_file; // Date of Schematics (on A2 file)
			$row[] = $obj->store_type_name; // STORE TYPE
			$row[] = $obj->city; // CITY
			$row[] = $obj->state; // STATE
			$row[] = !empty($obj->actual_date) ? $obj->actual_date : ''; // EVALUATION DATE
			$row[] = $first_name.' '.$last_name; // INSPECTOR NAME
			$row[] = !empty($obj->duration) ? $obj->duration : ''; // EVALUATION DURATION // JSON
			$row[] = !empty($obj->time_of_day) ? $obj->time_of_day : ''; // TIME OF DAY
			$row[] = !empty($obj->weather_condition) ? $obj->weather_condition : ''; // WEATHER STATUS

			// TODO - DEBUG
			// $row_debug = $row;
			// $row_test_data_values = array_keys($obj->row_test_data); // TODO - DEBUG
			// $row_debug = array_merge($row_debug, $row_test_data_values);
			// $result[] = $row_debug; 
			// echo json_encode($obj->row_test_data); die();
			
			$row_test_data_values = array_values($obj->row_test_data);
			$row = array_merge($row, $row_test_data_values);
			
			
			// ELEVATION
			
			$result[] = $row; 
		}
		
		// echo json_encode($result); die();
		
        return $result;
    }
	
	private function excel_write_test_data($test_code, $test_mapping){
		// $test_mapping = 
		
	}

	// NOT USED
    // public function collection()
    // {
        // return Plan::all();
    // }

    public function registerEvents(): array
    {
		
		
        return [
			
            BeforeWriting::class => function(BeforeWriting $event) {
                $templateFile = new LocalTemporaryFile(storage_path('/files/export_data.xlsx'));
                $event->writer->reopen($templateFile, \Maatwebsite\Excel\Excel::XLSX);
                $event->writer->getSheetByIndex(0);

                $this->calledByEvent = true; // set the flag
                $event->writer->getSheetByIndex(0)->export($event->getConcernable()); // call the export on the first sheet

                return $event->getWriter()->getSheetByIndex(0);
            },
        ];
    }
}
