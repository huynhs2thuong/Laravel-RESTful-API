<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Photo_tag
 *
 * @property int $id
 * @property string $sid
 * @property int $code
 * @property string $name
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Photo_tag extends Model
{
    protected $table = 'photo_tag';
    protected $guarded = ['id'];
}
