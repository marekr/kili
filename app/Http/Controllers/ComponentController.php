<?php namespace App\Http\Controllers;

use App\Component;
use App\KiCad\EeschemaComponent;

class ComponentController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}
	
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function index($id)
    {
		$c = Component::findOrFail($id);
        return view('component.index', ['component' => $c]);
    }
	
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function preview($id)
    {
		$c = Component::findOrFail($id);
		
		$raw = $c->raw;
		
		$comp = new EeschemaComponent();
		$comp->parseRaw( explode("\n",$raw) );
		
		$image = $comp->draw();
		echo $image;
		die();
    }
}
