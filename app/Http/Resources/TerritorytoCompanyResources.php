<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TerritorytoCompanyResources extends JsonResource
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
            'code'=> $this->code,
            'name'=>$this->name,
            'is_active' => $active,
        ];
    }
}
