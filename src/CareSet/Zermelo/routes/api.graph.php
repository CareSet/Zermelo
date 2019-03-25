<?php

use Illuminate\Support\Facades\Route;

// Tabular Report Management...
Route::get('/{report_key}/Download/{parameters?}', 'GraphApiController@download')->where( ['parameters' => '.*'] );
Route::get('/{report_key}/{parameters?}', 'GraphApiController@index')->where( ['parameters' => '.*'] );

