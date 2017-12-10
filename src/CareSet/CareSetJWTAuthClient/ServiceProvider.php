<?php


namespace CareSet\CareSetJWTAuthClient;

Class ServiceProvider extends Illuminate\Support\ServiceProvider
{

	public function register()
	{


	}


	public function boot()
	{
		Storage::MakeDirectory(base_path('keys'));
	    $this->publishes([
	        __DIR__.'/config/caresetjwtclient.php' => config_path('caresetjwtclient.php'),
	        __DIR__.'/keys/jwt_public_key.pub' => base_path('keys/jwt_public_key.pub'),
	    ]);
	}


}

