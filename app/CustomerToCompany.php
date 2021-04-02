<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CusstomerToCompany
 *
 * @property int $id
 * @property string $employee_sid
 * @property string $company_sid
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CusstomerToCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CusstomerToCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CusstomerToCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder|CusstomerToCompany whereCompanySid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CusstomerToCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CusstomerToCompany whereEmployeeSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CusstomerToCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CusstomerToCompany whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomerToCompany extends Model
{
    protected $table = 'customer_to_company';
    protected $guarded = ['id'];
}
