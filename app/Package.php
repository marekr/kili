<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model {

    public function libraries()
    {
        return $this->hasMany('App\Library');
    }

    public function events($limit = 30)
    {
        return $this->hasMany('App\PackageEvent')
                    ->orderBy('date_occurred', 'desc')
                    ->take($limit);
    }
}
