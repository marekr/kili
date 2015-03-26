<?php namespace App\Http\Controllers;

use Input;
use DB;
use App\Component;

class SearchController extends Controller {

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
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$q = Input::get('q');
		
		$searchTerms = explode(' ', $q);

		$query = null;

		foreach($searchTerms as $term)
		{
			if( $query == null )
			{
				$query = Component::with('library')->where('name', 'LIKE', '%'. $term .'%');
			}
			else
			{
				$query->where('name', 'LIKE', '%'. $term .'%');
			}
		}

		$results = $query->get();
		
		return view('search.index', ['query' => $q, 'results' => $results]);
	}

}
