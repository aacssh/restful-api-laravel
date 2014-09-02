<?php
Route::group(['prefix' => 'api/v1'], function(){

    Route::post('login', ['as' => 'api.v1.users.login', 'uses' => 'UsersController@store']);
    Route::get('logout', ['as' => 'api.v1.users.logout', 'uses' => 'UsersController@destroy']);
    Route::post('register', ['as' => 'api.v1.users.register', 'uses' => 'RegistersController@store']);
    Route::resource('barber', 'BarbersController', ['except' => ['store', 'create', 'edit']]);
    Route::resource('client', 'ClientsController', ['except' => ['store', 'create', 'edit']]);
    Route::resource('barber.appointments', 'AppointmentsController', ['except' => ['store', 'create', 'edit', 'update']]);
    Route::resource('client.appointments', 'AppointmentsController', ['except' => ['create', 'edit', 'update']]);
});