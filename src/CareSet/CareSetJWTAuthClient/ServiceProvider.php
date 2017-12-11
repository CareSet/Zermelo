<?php

namespace CareSet\CareSetJWTAuthClient;

use Illuminate\Support\Facades\Storage;

Class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

	public function register()
	{

	}

	public function boot(\Illuminate\Routing\Router $router)
	{

		 $this->loadMigrationsFrom(__DIR__.'/migrations');

		 $this->loadRoutesFrom(__DIR__.'/routes.php');

		$router->middleware('careset', \CareSet\CareSetJWTAuthClient\Middleware\JWTClientMiddleware::class);

		Storage::MakeDirectory(base_path('keys'));
		//Storage::delete(base_path('app/User.php'));

	    $this->publishes([
	        __DIR__.'/config/caresetjwtclient.php' => config_path('caresetjwtclient.php'),
	        __DIR__.'/keys/jwt_public_key.pub' => base_path('keys/jwt_public_key.pub'),
	       // __DIR__.'/Model/User.php' => base_path('app/User.php'),
	    ]);
	}

}

