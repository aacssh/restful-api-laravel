<?php
Route::group(['prefix' => 'api/v1'], function(){

    Route::post('login', ['as' => 'api.v1.users.login', 'uses' => 'UsersController@store']);
    Route::get('logout', ['as' => 'api.v1.users.logout', 'uses' => 'UsersController@destroy']);
    Route::post('register', ['as' => 'api.v1.users.register', 'uses' => 'RegistersController@store']);
    Route::resource('users', 'UsersController');
    Route::resource('barbers', 'BarbersController', ['except' => ['store', 'create', 'edit']]);
    Route::resource('clients', 'ClientsController', ['except' => ['store', 'create', 'edit']]);
    Route::resource('barbers.appointments', 'AppointmentsController', ['except' => ['store', 'create', 'edit', 'update']]);
    Route::resource('clients.appointments', 'AppointmentsController', ['except' => ['create', 'edit', 'update']]);
});