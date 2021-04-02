<?php

namespace App;

use Illuminate\Database\Eloquent\Model;





/**
 * App\Job
 *
 * @property int $id
 * @property string $sid
 * @property string $code
 * @property string $name
 * @property string $status
 * @property string $company_sid
 * @property int|null $store_count
 * @property int|null $store_completed_count
 * @property int|null $store_in_process_count
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $is_deleted
 * @method static \Illuminate\Database\Eloquent\Builder|Job newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Job newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Job query()
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereCompanySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereStoreCompletedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereStoreCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereStoreInProcessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Job extends Model
{
    protected $table = 'jobs';
    protected $guarded = ['id'];
}
