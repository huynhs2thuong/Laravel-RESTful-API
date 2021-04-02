<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\State_US
 *
 * @property int $ID
 * @property string $STATE_CODE
 * @property string $STATE_NAME
 * @method static \Illuminate\Database\Eloquent\Builder|State_US newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|State_US newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|State_US query()
 * @method static \Illuminate\Database\Eloquent\Builder|State_US whereID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State_US whereSTATECODE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State_US whereSTATENAME($value)
 * @mixin \Eloquent
 */
class State_US extends Model
{
    protected $table = 'us_states';
    protected $guarded = ['id'];
}
