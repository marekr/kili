<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model {

    public function libraries()
    {
        return $this->hasMany('App\Library');
    }

    public function events()
    {
        return $this->hasMany('App\PackageEvent');
    }
}
