<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StoreType
 *
 * @property int $id
 * @property string $sid
 * @property string $name
 * @property string $code
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType query()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereSid($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereUpdatedAt($value)
 */
class StoreType extends Model
{
    protected $guarded = ['id'];

    
}
