<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageEvent extends Model {

    public $timestamps = false;
    const TypeLibraryEdit = 'libedit';
    const TypeLibraryCreate = 'libcreate';
    const TypeLibraryDelete = 'libdelete';
    const TypeComponentEdit = 'componentedit';
    const TypeComponentCreate = 'componentcreate';
    const TypeComponentDelete = 'componentdelete';

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    private static function eventSetup(Package $package, $date = null)
    {
        $event = new PackageEvent;
        $event->package_id = $package->id;
        if( $date == null )
            $date = Carbon::now();
        $event->date_occurred = $date;

        return $event;
    }

    public static function addLibraryCreate(Library $library, $date = null)
    {
        $event = self::eventSetup($library->package, $date);
        $event->library_id = $library->id;

        $event->type = self::TypeLibraryCreate;
        $event->save();

        return $event;
    }

    public static function addLibraryEdit(Library $library, $date = null)
    {
        $event = self::eventSetup($library->package, $date);
        $event->library_id = $library->id;

        $event->type = self::TypeLibraryEdit;
        $event->save();

        return $event;
    }

    public static function addLibraryDelete(Library $library, $date = null)
    {
        $event = self::eventSetup($library->package, $date);
        $event->library_id = $library->id;

        $event->type = self::TypeLibraryDelete;
        $event->save();

        return $event;
    }

    public static function addComponentCreate(Component $component, $date = null)
    {
        $event = self::eventSetup($component->library->package, $date);
        $event->component_id = $component->id;
        $event->library_id = $component->library->id;

        $event->type = self::TypeComponentCreate;
        $event->save();

        return $event;
    }

    public static function addComponentEdit(Component $component, $date = null)
    {
        $event = self::eventSetup($component->library->package, $date);
        $event->component_id = $component->id;
        $event->library_id = $component->library->id;

        $event->type = self::TypeComponentEdit;
        $event->save();

        return $event;
    }

    public static function addComponentDelete(Component $component, $date = null)
    {
        $event = self::eventSetup($component->library->package, $date);
        $event->component_id = $component->id;
        $event->library_id = $component->library->id;

        $event->type = self::TypeComponentDelete;
        $event->save();

        return $event;
    }

    public function __toString()
    {
        $str = '';
        switch( $this->type )
        {
            case self::TypeComponentCreate:
                $str = 'Component created';
                break;
            case self::TypeComponentEdit:
                $str = 'Component edited';
                break;
            case self::TypeComponentDelete:
                $str = 'Component deleted';
                break;
            case self::TypeLibraryEdit:
                $str = 'Library edited';
                break;
            case self::TypeLibraryCreate:
                $str = 'Library created';
                break;
            case self::TypeLibraryDelete:
                $str = 'Library deleted';
                break;
            default:
                $str = '';
                break;
        }

        return $str;
    }
}
