<?php

namespace CareSet\CareSetReportEngine;

use Illuminate\Support\Facades\Storage;

Class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

	public function register()
	{

	}

	public function boot(\Illuminate\Routing\Router $router)
	{
	    $this->publishes([
	        __DIR__.'/config/caresetreportengine.php' => config_path('caresetreportengine.php'),
	        __DIR__.'/web.routes.php' => base_path('routes/caresetreportengine.web.example'),
	        __DIR__.'/api.routes.php' => base_path('routes/caresetreportengine.api.example'),
	    ]);
	}

}

