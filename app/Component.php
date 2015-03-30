<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Component extends Model {

    use SoftDeletes;

    public function library()
    {
        return $this->belongsTo('App\Library');
    }

	public function package()
	{
		return $this->library->package();
	}

    public function aliases()
    {
        return $this->hasMany('App\ComponentAlias');
    }

    public function events()
    {
        return $this->hasMany('App\PackageEvent')->orderBy('date_occurred', 'desc');
    }
}
