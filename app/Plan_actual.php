<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Plan_actual
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $sid
 * @property string $plan_sid
 * @property string $status
 * @property string $actual_date
 * @property int $is_manual
 * @property string $lat
 * @property string $lng
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereActualDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereIsManual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual wherePlanSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_actual whereUpdatedBy($value)
 */
class Plan_actual extends Model
{
    protected $guarded = ['id'];
    protected $table = 'plan_actual';
}
