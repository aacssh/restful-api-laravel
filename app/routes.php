<?php
Route::group(['prefix' => 'api/v1'], function(){
    Route::post('login', ['as' => 'api.v1.users.login', 'uses' => 'UsersController@login']);
    Route::delete('logout', ['as' => 'api.v1.users.logout', 'uses' => 'UsersController@destroy']);
    Route::post('register', ['as' => 'api.v1.users.register', 'uses' => 'UsersController@register']);
    Route::resource('barbers', 'BarbersController', ['except' => ['store', 'create', 'edit']]);
    Route::resource('clients', 'ClientsController', ['except' => ['store', 'create', 'edit']]);
    Route::resource('barbers.appointments', 'BarbersAppointmentsController', ['except' => ['store', 'create', 'edit', 'update']]);
    Route::resource('clients.appointments', 'ClientsAppointmentsController', ['except' => ['create', 'edit', 'update']]);
    Route::resource('barbers.shifts', 'ShiftsController', ['except' =>	['create', 'edit', 'destroy']]);
});