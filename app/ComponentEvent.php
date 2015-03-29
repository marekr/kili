<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ComponentEvent extends Model {

    public $timestamps = false;

    public function component()
    {
        return $this->belongsTo('App\Component');
    }

    public static function addCreated($componentID, $date = null)
    {
        $event = new ComponentEvent;
        $event->type = 'created';
        $event->component_id = $componentID;
        if( $date == null )
            $date = Carbon::now();
        $event->date_occurred = $date;
        $event->save();

        return $event;
    }

    public static function addEdited($componentID, $date = null)
    {
        $event = new ComponentEvent;
        $event->type = 'edited';
        $event->component_id = $componentID;
        if( $date == null )
            $date = Carbon::now();
        $event->date_occurred = $date;
        $event->save();

        return $event;
    }
}
