<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Component extends Model 
{
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
}
