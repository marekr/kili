<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ComponentAlias extends Model
{
    public function component()
    {
        return $this->belongsTo('App\Component');
    }
}
