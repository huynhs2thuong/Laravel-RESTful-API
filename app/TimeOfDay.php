<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TimeOfDay
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $sid
 * @property string $name
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay whereUpdatedAt($value)
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|TimeOfDay whereCode($value)
 */
class TimeOfDay extends Model
{
    protected $guarded = ['id'];
}
