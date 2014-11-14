<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*
Route::get('/', function()
{
	return View::make('index');
});
*/
Log::info('Route');

Route::get('/','Hello_xml@hello_gather');

Route::post('/','Hello_xml@hello_gather');


Route::get('hello','Hello_xml@hello');


Route::get('analysis','Analysis@analysis');

Route::post('analysis/gather','Analysis@analysis_gather');


Route::get('comments','Comments@comments');

Route::post('comments/submit','Comments@comments_submit');







