<?php


Route::get('/auth/cs/callback','\CareSet\CareSetJWTAuthClient\Controller\CareSetJWTLoginController@callback');

Route::middleware(['careset'])->group(function () {

	//all authenticated routes here





    Route::get('/logout','\CareSet\CareSetJWTAuthClient\Controller\CareSetJWTLoginController@logout');    
});