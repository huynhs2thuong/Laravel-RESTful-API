<?php

namespace App\Http\Resources;

use App\City_US;
use App\us_city;
use Illuminate\Http\Resources\Json\JsonResource;

class StateUSAResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->ID,
            'state_code'=>$this->STATE_CODE,
            'cities'=>City_US::whereid_state($this->ID)->select(['ID', 'CITY'])->first(),
        ];
    }
}
