<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



/**
 * App\Photo
 *
 * @property int $id
 * @property string $sid
 * @property string $plan_actual_sid
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $is_deleted
 * @property string $elevation_code
 * @property string $img_photo
 * @property string $name
 * @property string $description
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|Photo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereElevationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereImgPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo wherePlanActualSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Photo extends Model
{
    protected $table = 'photo';
    protected $guarded = ['id'];
}
