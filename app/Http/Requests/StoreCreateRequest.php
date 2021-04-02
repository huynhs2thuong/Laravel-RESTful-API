<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'phone' => 'required',
            'address_1' => 'required',
            'climate_region_sid' => 'required',
            'store_type_sid' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code'=> 'required',
            'territory_sid' => 'required',
            'company_sid' => 'required',
            'is_active' => 'required',
            // 'file_store' =>'mimes:txt'
        ];
    }
}
