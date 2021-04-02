<?php

namespace App;


use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Webpatser\Uuid\Uuid;


 
/**
 * App\Employee
 *
 * @property int $id
 * @property string $sid
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $password
 * @property string $phone_number
 * @property string $email
 * @property string|null $date_birth
 * @property string|null $title
 * @property string|null $address_1
 * @property string|null $address_2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property string|null $avatar
 * @property string $role
 * @property int $first_login
 * @property int $is_active
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDateBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFirstLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereZipCode($value)
 * @mixin \Eloquent
 * @property string|null $full_name
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFullName($value)
 */
class Employee extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $guarded = ['id'];
    protected $table = 'employees';

   
    // public function role() {
    //     return $this->belongsTo(Role::class);
    // }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        $user['role'] = $this->role;
        $user['sid'] = $this->sid;
         return [ 'user' => $user]; 
    }
}
