<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Library extends Model {

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function components()
    {
        return $this->hasMany('App\Component');
    }
}