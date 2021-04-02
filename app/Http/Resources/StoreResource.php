<?php

namespace App\Http\Resources;

use App\Climate_region;
use App\Company;
use App\StoreType;
use App\Territory;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       $store_type = StoreType::whereSid($this->store_type_sid)->select('sid','name')->first();
       $climate_region_store = Climate_region::whereSid($this->climate_region_sid)->select('sid','name')->first();
       $company = Company::whereSid($this->company_sid)->select('sid','name')->first();
       $tettitory = Territory::whereSid($this->territory_sid)->select('sid','name')->first();
       if($this->is_active == 1){
            $active = true;
        }else{
            $active = false;
        }
        return [
            'sid' => $this->sid,
            'plan_sid' => $this->plan_sid,
            'name'=> $this->name,
            'phone'=>$this->phone,
            'store_type' => $store_type,
            'climate_region_store' => $climate_region_store,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'city' => $this->city,
            'state' => $this->state,
            'location' => $this->city.','.$this->state,
            'zip_code' => $this->zip_code,
            'opening_hour' => $this->opening_hour,
            'img_store' => $this->img_store,
            'a2_file_number'=>$this->a2_file_number,
            'file_store' => $this->file_store,
            'a2_day_on_file' => $this->a2_day_on_file,
            'territory' => $tettitory,
            'company' => $company,
            'status' => $this->status,
            'is_active'=>$active,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
         ];
    }
}
