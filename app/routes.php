<?php
Route::group(['prefix' => 'api/v1'], function(){
    Route::post('login', 'UsersController@store');
    Route::post('logout', 'UsersController@logout');
    Route::post('register', 'UsersController@register');
    Route::resource('users', 'UsersController');
    Route::resource('barbers', 'BarbersController');
});