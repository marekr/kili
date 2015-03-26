<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Component extends Model {

	
    public function library()
    {
        return $this->belongsTo('App\Library');
    }

}
