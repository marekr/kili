<?php namespace App\Http\Controllers;

use App\Library;

class LibraryController extends Controller {

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
    public function overview($id)
    {
		$p = Library::findOrFail($id);
        return view('library.overview', ['library' => $p]);
    }
}
