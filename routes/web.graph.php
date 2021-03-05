<?php

Route::get( '/{report_key}/{parameters?}', 'GraphController@show' )->where(['parameters' => '.*']);
// We need to allow post to allow for submission of Data View Options, AKA "Sockets" form
Route::post( '/{report_key}/{parameters?}', 'GraphController@show' )->where(['parameters' => '.*']);
