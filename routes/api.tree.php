<?php

use Illuminate\Support\Facades\Route;

// Graph Report Management...
Route::any('/{report_key}/Download/{parameters?}', 'TreeApiController@download')->where( ['parameters' => '.*'] );
Route::any('/{report_key}/{parameters?}', 'TreeApiController@index')->where( ['parameters' => '.*'] );

