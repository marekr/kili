<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

Route::get('package/{id}', 'PackageController@overview');
Route::get('library/{id}', 'LibraryController@overview');
Route::get('component/{id}', 'ComponentController@index');
Route::get('component/{id}/preview', 'ComponentController@preview');
Route::get('search', 'SearchController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
