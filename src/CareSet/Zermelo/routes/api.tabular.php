<?php

use Illuminate\Support\Facades\Route;

// Tabular Report Management...
Route::get('/{report_key}/Download/{parameters?}', 'TabularApiController@download')->where( ['parameters' => '.*'] );
Route::get('/{report_key}/Summary/{parameters?}', 'TabularApiController@summary')->where( ['parameters' => '.*'] );
Route::get('/{report_key}/{parameters?}', 'TabularApiController@index')->where( ['parameters' => '.*'] );

