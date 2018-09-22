<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Image extends Model
{
    protected $table = 'images';

    public function location()
    {
        return $this->belongsTo('App\Location', 'location_id');
    }

    public function camera()
    {
        return $this->belongsTo('App\Camera', 'camera_id');
    }
}