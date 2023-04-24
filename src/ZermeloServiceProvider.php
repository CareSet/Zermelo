<?php

namespace CareSet\Zermelo;

use CareSet\Zermelo\Console\MakeGraphReportCommand;
use CareSet\Zermelo\Console\MakeTabularReportCommand;
use CareSet\Zermelo\Console\ZermeloDebugCommand;
use CareSet\Zermelo\Console\ZermeloInstallCommand;
use CareSet\Zermelo\Console\MakeCardsReportCommand;
use CareSet\Zermelo\Console\ZermeloReportCheckCommand;
use CareSet\Zermelo\Models\ZermeloDatabase;
use CareSet\Zermelo\Services\SocketService;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Class ZermeloServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /*
     * Registration happens before boot, so this is where we gather static configuration
     * and register things to be used later.
     */
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
            ZermeloDebugCommand::class,
	    ZermeloReportCheckCommand::class,
            MakeTabularReportCommand::class,
            MakeCardsReportCommand::class,
            MakeGraphReportCommand::class
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
                __DIR__.'/../config/zermelo.php', 'zermelo'
            );
        }
	}//end register function..


	public $is_socket_ok = false; //start assuming it is not.
	public $is_socket_checked = false;
    /**
     * @param Router $router
     *
     * This function is called after all providers have been registered,
     * and the database hass been set up.
     */
    public function boot( Router $router )
    {
        if (php_sapi_name() !== 'cli') {
            // Register the cache database connection if we have a zermelo db,
            // but only if we're running a web route, not during install commands
            $zermelo_cache_db = zermelo_cache_db();
            if (ZermeloDatabase::doesDatabaseExist($zermelo_cache_db)) {
                ZermeloDatabase::configure($zermelo_cache_db);
            }

            // Register and configure the config DB
            $zermelo_config_db = zermelo_config_db();
            if ( ZermeloDatabase::doesDatabaseExist( $zermelo_config_db ) ) {
                ZermeloDatabase::configure( $zermelo_config_db );
            }

            // Validate that there is only one is_default_socket for a wrench, throw an exception
            // if there is a wrench with Zero default sockets, or a wrench with more than one
            // default socket, as this can result unexpected behavior
            if (!$this->is_socket_checked) {
                $this->is_socket_ok = SocketService::checkIsDefaultSocket();
                $this->is_socket_checked = true;
            }
        }

        // routes

        // Boot our reports, but only in web mode. We don't care to register reports
        // during composer package discovery, or installation
        if (php_sapi_name() !== 'cli') {
            $this->registerApiRoutes();
            $this->registerWebRoutes();
            $this->registerReports();
            $this->loadViewsFrom( resource_path( 'views/zermelo' ), 'Zermelo');
        }
    }

    /**
     * Register the application's Zermelo reports.
     *
     * @return void
     */
    protected function registerReports()
    {
        $reportDir = report_path();
        if ( File::isDirectory($reportDir) ) {
            Zermelo::reportsIn( $reportDir );
        }
    }

    /**
     * Load the given routes file if routes are not already cached.
     *
     * @param  string  $path
     * @return void
     */
    protected function loadRoutesFrom($path)
    {
        if (! $this->app->routesAreCached()) {
            require $path;
        }
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerApiRoutes()
    {
        Route::group( $this->routeConfiguration(), function () {

            // Load the core zermelo api routes including sockets
            $this->loadRoutesFrom(__DIR__.'/../routes/api.sockets.php');

            $tabular_api_prefix = config('zermelo.TABULAR_API_PREFIX','Zermelo');
            Route::group( ['prefix' => $tabular_api_prefix ], function() {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.tabular.php');
            });

            $graph_api_prefix = config('zermelo.GRAPH_API_PREFIX','ZermeloGraph');
            Route::group( ['prefix' => $graph_api_prefix ], function() {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.graph.php');
            });

            $tree_api_prefix = config('zermelo.TREE_API_PREFIX','ZermeloTree');
            Route::group( ['prefix' => $tree_api_prefix ], function() {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.tree.php');
            });
        });
    }

    /**
     * Get the WEB route configurations
     */
    protected function registerWebRoutes()
    {
        Route::group($this->webRouteConfiguration(), function() {

            // Load the pretty-print SQL routes from web.sql.php using the configured prefix
            $sql_print_prefix = config( 'zermelo.SQL_PRINT_PREFIX','ZermeloSQL' );
            Route::group([ 'prefix' => $sql_print_prefix ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.sql.php');
            });

            Route::group([ 'prefix' => config('zermelo.CARD_URI_PREFIX', 'ZermeloCard') ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.card.php');
            });

            Route::group([ 'prefix' => config('zermelo.GRAPH_URI_PREFIX', 'ZermeloGraph') ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.graph.php');
            });

            Route::group([ 'prefix' => config('zermelo.TABULAR_URI_PREFIX', 'Zermelo') ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.tabular.php');
            });

            Route::group([ 'prefix' => config('zermelo.TREECARD_URI_PREFIX', 'ZermeloTreeCard') ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.tree_card.php');
            });
        });
    }

    /**
     * Get the API route group configuration array.
     *
     * @return array
     */
    protected function routeConfiguration()
    {
        $middleware = config('zermelo.MIDDLEWARE',[ 'api' ]);

        return [
            'namespace' => 'CareSet\Zermelo\Http\Controllers',
            'domain' => config('zermelo.domain', null),
            'as' => 'zermelo.api.',
            'prefix' => config( 'zermelo.API_PREFIX', 'zapi' ),
            'middleware' => $middleware,
        ];
    }

    /**
     * Get the web route group configuration array.
     *
     * @return array
     */
    protected function webRouteConfiguration()
    {
        $middleware = config('zermelo.WEB_MIDDLEWARE',[ 'web' ]);

        return [
            'namespace' => 'CareSet\Zermelo\Http\Controllers',
            //  'domain' => config('zermelo.domain', null),
            'as' => 'zermelo.web.',
            'prefix' => '',
            'middleware' => $middleware,
        ];
    }
}
