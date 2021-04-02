<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\City_US
 *
 * @property int $ID
 * @property int $ID_STATE
 * @property string $CITY
 * @method static \Illuminate\Database\Eloquent\Builder|City_US newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City_US newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City_US query()
 * @method static \Illuminate\Database\Eloquent\Builder|City_US whereCITY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City_US whereID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City_US whereIDSTATE($value)
 * @mixin \Eloquent
 */
class City_US extends Model
{
    protected $table = 'us_cities';
    protected $guarded = ['id'];
}
