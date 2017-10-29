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

Route::get('/', 'FrontController@redirectLocale');

Route::get('/image/{filename}', 'ImageController@view');
Route::get('/image/{width}/{height}/{filename}', 'ImageController@resize');
Route::get('/image/{left}/{top}/{width}/{height}/{filename}', 'ImageController@crop');

if (! App::runningInConsole()){
    Marble\Admin\App\Helpers\RouteHelper::generate();
}
