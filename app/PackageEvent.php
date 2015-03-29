<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageEvent extends Model {

    public $timestamps = false;

    public function package()
    {
        return $this->belongsTo('App\Package');
    }
}
