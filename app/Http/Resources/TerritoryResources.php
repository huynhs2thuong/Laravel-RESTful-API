<?php

namespace App\Http\Resources;

use App\Company;
use App\Store;
use Illuminate\Http\Resources\Json\JsonResource;

class TerritoryResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $total = 0;
        $count = Store::whereCompanySid($this->company_sid)->whereTerritorySid($this->sid)->count();
        //$total += $count;
        
        if($this->is_active == 1){
            $active = true;
        }else{
            $active = false;
        }
        return [
            'id' => $this->id,
            'sid' => $this->sid,
            'code'=> $this->code,
            'name'=>$this->name,
            'company'=>Company::whereSid($this->company_sid)->select(['sid', 'name'])->first(),
            'employee_list'=>!empty($this->employee_list) ? $this->employee_list : null,
            'store_count' => $count,
            'is_active' => $active,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
    }
}
