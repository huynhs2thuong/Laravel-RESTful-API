<?php

namespace App\Http\Resources;

use App\Company;
use App\Employee;
use App\Job;
use App\Photo_elevation;
use App\Photo_tag;
use App\Plan;
use App\Plan_actual;
use App\Store;
use App\Territory;
use DB;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(!empty($this->plan_actual_sid)){
            $plan_actual = Plan_actual::whereSid($this->plan_actual_sid)->first();
            $plain = Plan::whereSid($plan_actual->plan_sid)->first();
            $company = Company::whereSid($plain->company_sid)->select('sid','name')->first();
            $territory = Territory::whereSid($plain->territory_sid)->select('sid','name')->first();
            $job = Job::whereSid($plain->job_sid)->select('sid','name')->first();
            $store = Store::whereSid($plain->store_sid)->select('sid','name')->first();
            $employee = Employee::whereSid($plain->employee_sid)->select('sid','username')->first();
            $photo_tag = DB::select('select  photo_tag.sid, photo_tag.code ,photo_tag.name
            from photo_tag 
            inner join photo_job_tag on photo_job_tag.photo_tag_sid = photo_tag.sid 
            where photo_job_tag.photo_sid = "' .$this->sid.'"');
            $elevation = Photo_elevation::whereCode($this->elevation_code)->select('sid','code','name')->first();
            return [
                'id' => $this->id,
                'sid' => $this->sid,
                'name' => $this->name,
                'description' => $this->description,
                'img_photo'=>$this->img_photo,
                'elevation'=> $elevation,
                // 'photo_tags_sid'=> $this->photo_tags_sid,
                'photo_tags'=> $photo_tag,
                'company' => $company,
                'territories'=>$territory,
                'store'=>$store,
                'job'=>$job,

                'employee'=>$employee,
                'created_by'=>$this->created_by,
                'updated_by'=>$this->updated_by,
                'created_at'=>$this->created_at,
                'updated_at'=>$this->updated_at
            ];
        }
        
    }
}
