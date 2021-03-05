<?php

use Illuminate\Support\Facades\Route;

// Graph Report Management...
Route::any('/{report_key}/Download/{parameters?}', 'GraphApiController@download')->where( ['parameters' => '.*'] );
Route::any('/{report_key}/{parameters?}', 'GraphApiController@index')->where( ['parameters' => '.*'] );

