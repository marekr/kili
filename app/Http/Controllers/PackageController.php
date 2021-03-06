<?php namespace App\Http\Controllers;

use App\Package;
use Carbon\Carbon;

class PackageController extends Controller {

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
		$p = Package::findOrFail($id);
        return view('package.overview', ['package' => $p, 'page' => 'overview']);
    }

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function history($id)
    {
		$p = Package::findOrFail($id);
		$events = $p->eventsPaginated();

		$eventsGrouped = $events->groupBy(function($date) {
        	return Carbon::parse($date->date_occurred)->format('y-m-d');
    	});
		
        return view('package.history', ['package' => $p,
										'page' => 'history',
										'events' => $eventsGrouped->toArray(),
										'eventspaged' => $events
										]);
    }
}
