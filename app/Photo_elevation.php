<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Photo_elevation
 *
 * @property int $id
 * @property string $sid
 * @property string $code
 * @property string $name
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_elevation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Photo_elevation extends Model
{
    protected $table = 'photo_elevations';
    protected $guarded = ['id'];
}
