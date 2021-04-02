<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
           'id' => $this->id,
           'sid' => $this->sid,
           'name'=> $this->name,
           'tax' => $this->tax,
           'email' => $this->email,
           'fax' => $this->fax,
           'phone' => $this->phone,
           'address_1' => $this->address_1,
           'address_2' => $this->address_2,
           'city' => $this->city,
           'state' => $this->state,
           'location' => $this->city.','.$this->state,
           'zip_code' => $this->zip_code,
           'is_active'=>$active,
           'created_at'=>$this->created_at,
           'updated_at'=>$this->updated_at
        ];
    }
}
