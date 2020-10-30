<?php
/*
 * This routes file is used for the doctrine sql-fomatter "pretty-print" routes
 */

use Illuminate\Support\Facades\Route;

Route::get( '/{report_key}/{parameters?}', 'SQLPrintController@show' )->where(['parameters' => '.*']);
