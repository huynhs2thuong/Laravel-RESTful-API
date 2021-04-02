<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Direction
 *
 * @property int $id
 * @property string $sid
 * @property string $name
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Direction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Direction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Direction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Direction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Direction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Direction whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Direction whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Direction whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Direction whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|Direction whereCode($value)
 */
class Direction extends Model
{
    protected $table = 'directions';
    protected $guarded = ['id'];
}
