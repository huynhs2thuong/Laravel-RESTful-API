<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JsonStructoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $json_data = json_decode($this->content, true);
        
        return [
            'id' => $this->id,
            'code'=> $this->code,
            'content'=>$json_data,
        ];
    }
}
