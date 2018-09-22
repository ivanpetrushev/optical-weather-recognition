<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Camera extends Model
{
    protected $table = 'cameras';
    public $timestamps = false;

    public function location()
    {
        return $this->belongsTo('App\Location', 'location_id');
    }

    public function images()
    {
        return $this->hasMany('App\Image', 'camera_id');
    }

    public function cameras()
    {
        return $this->hasMany('App\Camera', 'camera_id');
    }
}