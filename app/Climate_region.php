<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Climate_region
 *
 * @property int $id
 * @property string $sid
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region query()
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region whereIsActive($value)
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|Climate_region whereCode($value)
 */
class Climate_region extends Model
{
    protected $table = 'climate_region_store';
    protected $guarded = ['id'];
}
