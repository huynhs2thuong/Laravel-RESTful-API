<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Plan_info
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $sid
 * @property string $status
 * @property string $last_chek_in
 * @property string $last_chek_out
 * @property int $is_manual
 * @property string $plan_actual_sid
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $is_deleted
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereIsManual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereLastChekIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereLastChekOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info wherePlanActualSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan_info whereUpdatedBy($value)
 */
class Plan_info extends Model
{
    protected $guarded = ['id'];
    protected $table = 'plan_info';
}
