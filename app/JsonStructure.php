<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\JsonStructure
 *
 * @property int $id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $code
 * @property string $content
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure query()
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JsonStructure whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class JsonStructure extends Model
{
    protected $guarded = ['id'];
}
