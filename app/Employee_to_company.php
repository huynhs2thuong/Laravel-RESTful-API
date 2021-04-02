<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Employee_to_company
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $employee_sid
 * @property string $company_sid
 * @property string $territory_sid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company whereCompanySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company whereEmployeeSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company whereTerritorySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company whereUpdatedAt($value)
 * @property int|null $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|Employee_to_company whereIsActive($value)
 */
class Employee_to_company extends Model
{
    protected $guarded = ['id'];
}
