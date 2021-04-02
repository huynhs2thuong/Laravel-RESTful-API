<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Weather
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Weather newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Weather newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Weather query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $sid
 * @property string $name
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Weather whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weather whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weather whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weather whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weather whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weather whereUpdatedAt($value)
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|Weather whereCode($value)
 */
class Weather extends Model
{
    protected $guarded = ['id'];
}
