<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Territory
 *
 * @property int $id
 * @property string $sid
 * @property string $code
 * @property string $name
 * @property int $is_active
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Territory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Territory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Territory query()
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereDeletedBy($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereSid($value)
 * @property string $company_sid
 * @method static \Illuminate\Database\Eloquent\Builder|Territory whereCompanySid($value)
 * @property-read \App\Company $company
 */
class Territory extends Model
{
    protected $guarded = ['id'];

    public function company() {
        return $this->belongsTo(Company::class)->select(['sid', 'name','email','phone','address_1','address_2','city','state','zip_code','is_active']);
    }
}
