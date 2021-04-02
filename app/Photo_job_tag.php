<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Photo_job_tag
 *
 * @property int $id
 * @property string $photo_sid
 * @property string $photo_tag_sid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job_tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job_tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job_tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job_tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job_tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job_tag wherePhotoSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job_tag wherePhotoTagSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Photo_job_tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Photo_job_tag extends Model
{
    protected $table = 'photo_job_tag';
    protected $guarded = ['id'];
}
