<?php

namespace App\Http\Resources;

use DB;
use Illuminate\Http\Resources\Json\JsonResource;

class ListPhotoResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $employee['sid'] = $this->employees_sid;
        $employee['username'] = $this->employees_name;
        $company['sid'] = $this->companies_sid;
        $company['name'] = $this->companies_name;
        $territory['sid'] = $this->territories_sid;
        $territory['name'] = $this->territories_name;
        $store['sid'] = $this->stores_sid;
        $store['name'] = $this->stores_name;
        $job['sid'] = $this->jobs_sid;
        $job['name'] = $this->jobs_name;
        $elevation['sid'] = $this->photo_elevations_sid;
        $elevation['code'] = $this->photo_elevations_code;
        $elevation['name'] = $this->photo_elevations_name;
        $photo_tag = DB::select('select  photo_tag.sid, photo_tag.code ,photo_tag.name
            from photo_tag 
            inner join photo_job_tag on photo_job_tag.photo_tag_sid = photo_tag.sid 
            where photo_job_tag.photo_sid = "' .$this->sid.'"');

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
            'territory'=>$territory,
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
