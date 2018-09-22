<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Image extends Model
{
    protected $table = 'images';

    public function location()
    {
        return $this->hasMany('App\Location', 'location_id');
    }

    public function camera()
    {
        return $this->hasMany('App\Camera', 'camera_id');
    }
}