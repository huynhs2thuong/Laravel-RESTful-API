<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Plan_data
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $sid
 * @property string $data
 * @property string $pg_id
 * @property string $plan_actual_sid
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data wherePgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data wherePlanActualSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_data whereUpdatedBy($value)
 */
class Plan_data extends Model
{
    protected $guarded = ['id'];
    protected $table = 'plan_data';
}
