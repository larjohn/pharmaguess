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

Route::get('play', function () {
    return view('welcome');
});

Route::get('mol', function () {
    return view('mol');
});

Route::post('data.json', ['uses'=>'DataController@get', 'as'=>'data']);
Route::get('mol/{id}.sdf', ['uses'=>'DataController@getMolecule', 'as'=>'mol']);