<?php


Route::middleware('web')->group(function() {
	
	Route::get('/auth/cs/callback','\CareSet\CareSetJWTAuthClient\Controller\CareSetJWTLoginController@callback');
	Route::get('/logout','\CareSet\CareSetJWTAuthClient\Controller\CareSetJWTLoginController@logout');    

});

Route::middleware(['careset'])->group(function () {

	//all authenticated routes here





});