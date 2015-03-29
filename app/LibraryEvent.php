<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LibraryEvent extends Model {

    public $timestamps = false;

    public function library()
    {
        return $this->belongsTo('App\Library');
    }

    public static function addCreated($libraryID, $date = null)
    {
        $event = new LibraryEvent;
        $event->type = 'created';
        $event->library_id = $libraryID;
        if( $date == null )
            $date = Carbon::now();
        $event->date_occurred = $date;
        $event->save();

        return $event;
    }

    public static function addEdited($libraryID, $date = null)
    {
        $event = new LibraryEvent;
        $event->type = 'edited';
        $event->library_id = $libraryID;
        if( $date == null )
            $date = Carbon::now();
        $event->date_occurred = $date;
        $event->save();

        return $event;
    }
}
