<?php

use Illuminate\Support\Facades\Route;

Route::get('/wrenches', 'SocketWrenchController@index');
Route::post('/wrench/{wrench_id}/socket/{socket_id}', 'SocketWrenchController@activateSocket');
Route::post( '/socketwrenches/submit', 'SocketWrenchController@formSubmit' );
