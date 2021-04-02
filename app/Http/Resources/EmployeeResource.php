<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->is_active == 1){
            $active = true;
        }else{
            $active = false;
        }
        return [
            'sid' => $this->sid,
            'username'=>$this->username,
            'phone_number'=>$this->phone_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name. ' '. $this->last_name,
            'email' => $this->email,
            'title'=>$this->title,
            'date_birth'=>$this->date_birth,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'avatar' => $this->avatar,
            'role'=> $this->role,
            'first_login' => $this->first_login,
            'is_active'=>$active,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
    }
}
