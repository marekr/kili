<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Library extends Model {

    use SoftDeletes;

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function components()
    {
        return $this->hasMany('App\Component');
    }

    public function events()
    {
        return $this->hasMany('App\PackageEvent')->orderBy('date_occurred', 'desc');
    }
}
