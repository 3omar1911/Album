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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['auth']], function() {
	Route::get('/albums/create', ['as' => 'albums.create', 'uses' => 'AlbumsController@create']);
	Route::post('/albums', ['as' => 'albums.store', 'uses' => 'AlbumsController@store']);
});
