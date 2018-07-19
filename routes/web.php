<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['requestToken']], function ($route) {

    $route->get('/tags', 'GirlsController@tags')->name('girls.tags.list');

    $route->get('/girls', 'GirlsController@imageList')->name('girls.image.list');

    $route->get('/girlsInfo', 'GirlsController@image')->name('girls.image.info');
});
