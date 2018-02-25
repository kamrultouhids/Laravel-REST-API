<?php

use Illuminate\Http\Request;


Route::post('login','ApiLoginController@login');

Route::group(['middleware' => 'jwt.auth'], function () {

    Route::get('user','ApiUserController@index');
    Route::post('user','ApiUserController@store');
    Route::get('user/{id}','ApiUserController@edit');
    Route::put('user/{id}','ApiUserController@update');
    Route::delete('user/{id}','ApiUserController@destroy');

});
