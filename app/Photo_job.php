<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Photo_job
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $sid
 * @property string $job_sid
 * @property string $store_sid
 * @property string $territory_sid
 * @property string $elevation_code
 * @property string $original_file_name
 * @property string $name
 * @property string $description
 * @property string $file
 * @property int $is_active
 * @property string $plan_actual_sid
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $is_deleted
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereElevationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereJobSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereOriginalFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job wherePlanActualSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereStoreSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereTerritorySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job whereUpdatedBy($value)
 */
class Photo_job extends Model
{
    protected $table = 'photo_job';
    protected $guarded = ['id'];
}
