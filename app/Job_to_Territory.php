<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Job_to_Territory
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $job_sid
 * @property string $territory_sid
 * @property string $company_sid
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory whereCompanySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory whereJobSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory whereTerritorySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job_to_Territory whereUpdatedAt($value)
 */
class Job_to_Territory extends Model
{
    protected $table = 'job_to_territories';
    protected $guarded = ['id'];
}
