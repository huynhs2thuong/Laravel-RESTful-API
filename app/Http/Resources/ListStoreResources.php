<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListStoreResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $store_type['sid'] = $this->store_types_sid;
        $store_type['name'] = $this->store_types_name;
        $climate_region_store['sid'] = $this->climate_region_store_sid;
        $climate_region_store['name'] = $this->climate_region_store_name;
        $company['sid'] = $this->company_sid;
        $company['name'] = $this->company_name;
        $tettitory['sid'] = $this->territory_sid;
        $tettitory['name'] = $this->territory_name;
        
       if($this->is_active == 1){
            $active = true;
        }else{
            $active = false;
        }
        return [
            'sid' => $this->sid,
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
            'is_active'=>$active,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
         ];    
    }
}
