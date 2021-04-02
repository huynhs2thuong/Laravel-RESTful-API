<?php

namespace App\Http\Resources;

use App\Employee;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //q$username = Employee::whereid($this->causer_id)->select('id','username')->first();
        $username['sid'] = $this->employee_sid;
        $username['name'] = $this->employee_username;
        $username['avatar'] = $this->employee_avatar;

        return [
            'id' => $this->id,
            'description'=>$this->description,
            'subject_type'=>$this->subject_type,
            'causer_id'=>$this->causer_id,
            'username' => $username,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}
