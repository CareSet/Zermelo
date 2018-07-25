<?php

namespace CareSet\Zermelo;

use CareSet\Zermelo\Console\ZermeloInstallCommand;
use CareSet\Zermelo\Console\ZermeloMakeDemoCommand;
use CareSet\Zermelo\Console\ZermeloMakeReportCommand;
use CareSet\Zermelo\Interfaces\ControllerInterface;
use CareSet\Zermelo\Models\ReportFactory;
use CareSet\Zermelo\Models\ZermeloReport;
use Illuminate\Routing\Router;

Class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function register()
	{
        require_once __DIR__ . '/helpers.php';

        // I call the method like this for the request to be injected in the registerPresenter method.
        $this->app->call([$this, 'registerOnRequest']);
	}

    public function registerOnRequest( \Illuminate\Http\Request $request )
    {
        // Create the presenter repository singleton
        $this->app->singleton( 'CareSet\Zermelo\Models\ControllerRepository' );

        $this->app->bind(ZermeloReport::class, function ($app) use ($request) {

            // I use $app->make so processor classes dependancies gets resolved and injected.
            $report = ReportFactory::build( $request );
            return $report;
        });
    }

	public function boot( Router $router )
	{

        /*
         * Register our zermelo view make command which:
         *  - Copies views
         *  - Exports configuration
         *  - Exports Assets
         */
        $this->commands([
            ZermeloInstallCommand::class,
            ZermeloMakeReportCommand::class
        ]);

	    /*
	     * Merge with main config so parameters are accessable.
	     * Try to load config from the app's config directory first,
	     * then load from the package.
	     */
        if ( file_exists(  config_path( 'zermelo.php' ) ) ) {
            $this->mergeConfigFrom(
                config_path( 'zermelo.php' ), 'zermelo'
            );
        } else {
            $this->mergeConfigFrom(
                __DIR__.'/config/zermelo.php', 'zermelo'
            );
        }

        // Get the array of controllers form the controller repo
        $controllerRepo = $this->app->make( 'CareSet\Zermelo\Models\ControllerRepository' );

        // Create the controller route dynamically using the ControllerInterface
        foreach ( $controllerRepo->all() as $prefix => $controller ) {
            if ( $controller instanceof ControllerInterface ) {
                $module_route = $controller->prefix();
                $router->get( "/$module_route/{report_name}/{parameters?}", function ( ZermeloReport $report ) use ( $controller ) {
                    return $controller->show( $report );
                } );
            }
        }
	}
}
