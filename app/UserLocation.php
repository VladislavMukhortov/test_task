<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserLocation
 * @package App
 *
 * @property float latitude
 * @property float longitude
 */
class UserLocation extends Model
{
    protected $fillable = [
        'latitude',
        'longitude',
        'address'
    ];
}
