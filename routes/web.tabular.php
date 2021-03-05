<?php

Route::get( '/{report_key}/{parameters?}', 'TabularController@show' )->where(['parameters' => '.*']);
Route::post( '/{report_key}/{parameters?}', 'TabularController@show' )->where(['parameters' => '.*']);
