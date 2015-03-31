<?php namespace App;

use Illuminate\Database\Eloquent\Model;


class Library extends Model {

    public static function boot()
    {
        parent::boot();

        static::deleted(function($library)
        {
            $library->events()->update(array('library_id' => NULL, 'component_id' => NULL));
            $library->components()->delete();
        });
    }

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
        return $this->hasMany('App\PackageEvent')
                    ->orderBy('date_occurred', 'desc');
    }

    public function eventsPaginated()
    {
        return $this->events()->paginate(60);
    }
}
