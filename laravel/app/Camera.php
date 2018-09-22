<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Camera extends Model
{
    protected $table = 'cameras';

    public function location()
    {
        return $this->hasMany('App\Location', 'location_id');
    }
}