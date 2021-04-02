<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Plan
 *
 * @property int $id
 * @property string $sid
 * @property string $code
 * @property int $is_manual
 * @property string $company_sid
 * @property string $job_sid
 * @property string $store_sid
 * @property string $territory_sid
 * @property string $employee_sid
 * @property string $status
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCompanySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereEmployeeSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereIsManual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereJobSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereStoreSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereTerritorySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUpdatedBy($value)
 * @mixin \Eloquent
 * @property int $is_active
 * @property int $is_reject
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereIsReject($value)
 */
class Plan extends Model
{
    protected $guarded = ['id'];
}
