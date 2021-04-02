<?php

namespace App\Http\Resources;

use App\Company;
use App\Job_to_Territory;
use App\Store;
use App\Territory;
use DB;
use Illuminate\Http\Resources\Json\JsonResource;

class JobEmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $company = Company::whereSid($this->company_sid)->select('sid','name')->first();
        $territories = Territory::whereSid($this->territory_sid)->select('sid','name')->first();
        // $territories = DB::select('select  territories.sid, territories.code ,territories.name
        // from territories 
        // inner join job_to_territories on territories.sid = job_to_territories.territory_sid 
        // where territories.company_sid = "' .$this->company_sid.'"
        // and job_to_territories.job_sid = "' .$this->sid.'"');
        // $total = 0;
        // $count = Store::whereCompanySid($this->company_sid)->whereTerritorySid($territories->sid)->count();
        //    $total += $count;
        // foreach($territories as $item){
        //     $count = Store::whereCompanySid($this->company_sid)->whereTerritorySid($item->sid)->count();
        //     $total += $count;
        // }
        $store_in_progress_list = !empty($this->in_progress_mapping) ? array_values($this->in_progress_mapping) : [];
        return [
            'id' => $this->id,
            'sid' => $this->sid,
            'code'=>$this->code,
            'name'=> $this->name,
            'company' => $company,
            'territories'=>$territories,
            'status'=>$this->status,
            // 'store_count' => $count,
            'store_count' => (int)$this->store_count,
            'store_completed_count' => (int)$this->store_completed_count,
            'store_in_process_count' => (int)$this->store_in_process_count,
            'store_in_progress_list' => $store_in_progress_list,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
        return parent::toArray($request);
    }
}
