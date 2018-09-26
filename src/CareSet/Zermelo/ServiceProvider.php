<?php

namespace CareSet\Zermelo;

use CareSet\Zermelo\Console\ZermeloInstallCommand;
use CareSet\Zermelo\Console\ZermeloMakeDemoCommand;
use CareSet\Zermelo\Console\ZermeloMakeReportCommand;
use CareSet\Zermelo\Interfaces\ControllerInterface;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ReportFactory;
use CareSet\Zermelo\Models\ZermeloDatabase;
use CareSet\Zermelo\Models\ZermeloReport;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Cloner\Data;

Class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function register()
	{
        require_once __DIR__ . '/helpers.php';

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

        // Register the cache database connection if we have a zermelo db
        $zermelo_db = config( 'zermelo.ZERMELO_DB' );
        if ( ZermeloDatabase::doesDatabaseExist( $zermelo_db ) ) {
            ZermeloDatabase::configure( $zermelo_db );
        }

        // I call the method like this for the request to be injected in the registerPresenter method.
        $this->app->call([$this, 'registerOnRequest']);
	}

    public function registerOnRequest( \Illuminate\Http\Request $request )
    {
        // Create the presenter repository singleton
        $this->app->singleton( 'CareSet\Zermelo\Models\ControllerRepository' );
    }

	public function boot( Router $router )
	{
        // Get the array of controllers form the controller repo
        $controllerRepo = $this->app->make( 'CareSet\Zermelo\Models\ControllerRepository' );

        // Create the controller route dynamically using the ControllerInterface
        foreach ( $controllerRepo->all() as $prefix => $controller ) {
            if ( $controller instanceof ControllerInterface ) {
                $module_route = $controller->prefix();
                $router->get( "/$module_route/{report_name}/{parameters?}", function ( Request $request, $report_name, $parameters = "" ) use ( $controller ) {
                    $report = ReportFactory::build( $request, $report_name, $parameters );
                    return $controller->show( $report );
                } )->where( ['parameters' => '.*'] );
            }
        }
	}
}
