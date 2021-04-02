<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\ChangePassword
 *
 * @property int $id
 * @property string $email
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ChangePassword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangePassword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangePassword query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangePassword whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangePassword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangePassword whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangePassword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangePassword whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ChangePassword extends Model
{
    protected $table = 'change_passwords';
    protected $guarded = ['id'];
}
