<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model {

    public function libraries()
    {
        return $this->hasMany('App\Library');
    }

    public function librariesOrdered()
    {
        return $this->libraries()->orderBy('name', 'asc');
    }

    public function events()
    {
        return $this->hasMany('App\PackageEvent')
                    ->orderBy('date_occurred', 'desc');
    }

    public function eventsPaginated()
    {
        return $this->events()->paginate(60);
    }
}
