<?php

namespace App\Http\Resources;

use App\State_US;
use App\us_city;
use App\us_state;
use Illuminate\Http\Resources\Json\JsonResource;

class CityUSAResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $state = State_US::whereid($this->ID_STATE)->select(['ID','STATE_NAME','STATE_CODE'])->first();
        return [
            'id' => $this->ID,
            'city'=>$this->CITY,
            'state'=>$state->STATE_CODE
        ];
    }
}
