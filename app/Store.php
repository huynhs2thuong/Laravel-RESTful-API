<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

 
/**
 * App\Store
 *
 * @property int $id
 * @property string $sid
 * @property string $company_sid
 * @property string $territory_sid
 * @property string $name
 * @property string $phone
 * @property string $climate_region_sid
 * @property string|null $store_type_sid
 * @property string $address_1
 * @property string|null $address_2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property string|null $img_store
 * @property string|null $file_store
 * @property string|null $a2_file_number
 * @property string|null $a2_day_on_file
 * @property string|null $opening_hour
 * @property int $is_active
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Store newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Store query()
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereA2DayOnFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereA2FileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereClimateRegionSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereCompanySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereFileStore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereImgStore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereOpeningHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereStoreTypeSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereTerritorySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereZipCode($value)
 * @mixin \Eloquent
 */
class Store extends Model
{
    protected $table = 'stores';
    protected $guarded = ['id'];

}
