<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ComponentEvent extends Model {

    public $timestamps = false;

    public function component()
    {
        return $this->belongsTo('App\Component');
    }
}
