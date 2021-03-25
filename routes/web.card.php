<?php

Route::get( '/{report_key}/{parameters?}', 'CardController@show' )->where(['parameters' => '.*']);
Route::post( '/{report_key}/{parameters?}', 'CardController@show' )->where(['parameters' => '.*']);
