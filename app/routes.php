<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This file contains all of the routes for HairConnect application.
|
*/
/**
 * Below function groups all the routes and prefixies them (routes) with 'api/v1'
 */
Route::group(['prefix' => 'api/v1'], function(){
    Route::post('login', ['as' => 'api.v1.users.login', 'uses' => 'AccountsController@login']);
    Route::delete('logout', ['as' => 'api.v1.users.logout', 'uses' => 'AccountsController@destroy']);
    Route::post('register', ['as' => 'api.v1.users.register', 'uses' => 'AccountsController@register']);
    Route::put('change-password', ['as' => 'api.v1.users.change.password', 'uses' => 'AccountsController@update']);
    Route::post('forgot-password', ['as' => 'api.v1.users.forgot.password', 'uses' => 'AccountsController@forgotPassword']);
    Route::get('recover/{code}', ['as' => 'api.v1.users.recover', 'uses' => 'AccountsController@recover']);
    Route::post('barbers/search', ['as' => 'api.v1.barbers.search', 'uses' => 'BarbersController@search']);
    Route::resource('barbers', 'BarbersController', ['except' => ['store', 'create', 'edit']]);
    Route::resource('clients', 'ClientsController', ['except' => ['store', 'create', 'edit']]);
    Route::resource('barbers.appointments', 'BarbersAppointmentsController', ['except' => ['store', 'create', 'edit', 'update']]);
    Route::resource('clients.appointments', 'ClientsAppointmentsController', ['except' => ['create', 'edit', 'update']]);
    Route::resource('barbers.shifts', 'ShiftsController', ['except' =>	['create', 'edit', 'destroy']]);
    Route::resource('barbers.images', 'ImagesController', ['except' =>  ['create', 'edit', 'destroy']]);
});