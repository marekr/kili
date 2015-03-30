<?php namespace App;

use Illuminate\Database\Eloquent\Model;


class Component extends Model {

    public static function boot()
    {
        parent::boot();

        static::deleted(function($component)
        {
            $component->events()->update(array('component_id' => NULL));
            $component->aliases()->delete();
        });
    }

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
