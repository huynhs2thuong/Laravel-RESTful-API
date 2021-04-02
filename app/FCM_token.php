<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FCM_token
 *
 * @property int $id
 * @property string $sid
 * @property string $employee_sid
 * @property string $fcm_token
 * @property int $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token query()
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token whereEmployeeSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token whereFcmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FCM_token whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FCM_token extends Model
{
    protected $table = 'fcm_tokens';
    protected $guarded = ['id'];
}
