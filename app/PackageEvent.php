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
        $event->library_name = $library->name;

        $event->type = self::TypeLibraryCreate;
        $event->save();

        return $event;
    }

    public static function addLibraryEdit(Library $library, $date = null)
    {
        $event = self::eventSetup($library->package, $date);
        $event->library_id = $library->id;
        $event->library_name = $library->name;

        $event->type = self::TypeLibraryEdit;
        $event->save();

        return $event;
    }

    public static function addLibraryDelete(Library $library, $date = null)
    {
        $event = self::eventSetup($library->package, $date);
        $event->library_id = $library->id;
        $event->library_name = $library->name;

        $event->type = self::TypeLibraryDelete;
        $event->save();

        return $event;
    }

    public static function addComponentCreate(Component $component, $date = null)
    {
        $event = self::eventSetup($component->library->package, $date);
        $event->component_id = $component->id;
        $event->component_name = $component->name;
        $event->library_id = $component->library->id;
        $event->library_name = $component->library->name;

        $event->type = self::TypeComponentCreate;
        $event->save();

        return $event;
    }

    public static function addComponentEdit(Component $component, $date = null)
    {
        $event = self::eventSetup($component->library->package, $date);
        $event->component_id = $component->id;
        $event->component_name = $component->name;
        $event->library_id = $component->library->id;
        $event->library_name = $component->library->name;

        $event->type = self::TypeComponentEdit;
        $event->save();

        return $event;
    }

    public static function addComponentDelete(Component $component, $date = null)
    {
        $event = self::eventSetup($component->library->package, $date);
        $event->component_id = $component->id;
        $event->component_name = $component->name;
        $event->library_id = $component->library->id;
        $event->library_name = $component->library->name;

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
                $str = 'Component "%s" in library "%s" created';
                $str = sprintf($str, $this->component_name, $this->library_name);
                break;
            case self::TypeComponentEdit:
                $str = 'Component "%s" in library "%s" edited';
                $str = sprintf($str, $this->component_name, $this->library_name);
                break;
            case self::TypeComponentDelete:
                $str = 'Component "%s" in library "%s" deleted';
                $str = sprintf($str, $this->component_name, $this->library_name);
                break;
            case self::TypeLibraryEdit:
                $str = 'Library "%s" edited';
                $str = sprintf($str, $this->library_name);
                break;
            case self::TypeLibraryCreate:
                $str = 'Library "%s" created';
                $str = sprintf($str, $this->library_name);
                break;
            case self::TypeLibraryDelete:
                $str = 'Library "%s" deleted';
                $str = sprintf($str, $this->library_name);
                break;
            default:
                $str = '';
                break;
        }

        return $str;
    }
}
