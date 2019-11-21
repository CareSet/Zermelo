<?php

namespace CareSet\Zermelo;

use CareSet\Zermelo\Console\ZermeloDebugCommand;
use CareSet\Zermelo\Console\ZermeloInstallCommand;
use CareSet\Zermelo\Console\ZermeloMakeDemoCommand;
use CareSet\Zermelo\Console\MakeCardsReportCommand;
use CareSet\Zermelo\Models\ZermeloDatabase;
use CareSet\Zermelo\Services\SocketService;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Class ZermeloServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected function presentations()
    {
        return [];
    }

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
            ZermeloDebugCommand::class
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
        $no_cache_database = false;
        $zermelo_cache_db = zermelo_cache_db();
        if ( ZermeloDatabase::doesDatabaseExist( $zermelo_cache_db ) ) {
            ZermeloDatabase::configure( $zermelo_cache_db );
        } else {
            $no_cache_database = true;
        }

        // Register and configure the config DB
        $no_config_database = false;
        $zermelo_config_db = zermelo_config_db();
        if ( ZermeloDatabase::doesDatabaseExist( $zermelo_config_db ) ) {
            ZermeloDatabase::configure( $zermelo_config_db );
        } else {
            $no_config_database = true;
        }

        // The following block spits out an error message that indicates why Zermelo probably couldn't connect
        // to the cache and config databases, due to permissions error. If we are running in web server, tell
        // user to check the .env file, if we are running an artisan command, we can provide more information
        // about what user is attempting to connect and potentially how to fix the issue.
        if ( $no_cache_database === true ||
            $no_config_database === true) {
            $message = "Zermelo is unable to connect to cache or config database,\n";
            $message .= "Please check the username and password in your .env file's database credentials and try again.\n";

            // If We are running install/client mode output some more information
            if (php_sapi_name() == 'cli') {
                $default = config( 'database.default' );

                $username = config( "database.connections.$default.username" );
                $message .= "You are trying to connect with mysql user `$username`, you may have to run the following commands:\n";
		$message .= "
GRANT 
      SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES 
ON `_zermelo_cache`.* 
TO '$username'@'localhost' 
;
";
		$message .= "
GRANT 
   SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES 
ON `_zermelo_config`.* 
TO '$username'@'localhost' 
;
";

            }
            throw new \Exception($message);
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
        	// Validate that there is only one is_default_socket for a wrench, throw an exception
        	// if there is a wrench with Zero default sockets, or a wrench with more than one
        	// default socket, as this can result unexpected behavior
		if(!$this->is_socket_checked){
        		$this->is_socket_ok = SocketService::checkIsDefaultSocket();
			$this->is_socket_checked = true;
		}

        	// routes

        	// Boot our reports, but only in web mode. We don't care to register reports
        	// during composer package discovery, or installation
        	if (php_sapi_name() !== 'cli') {
            		$this->registerApiRoutes();
            		$this->registerReports();
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
            $this->loadRoutesFrom(__DIR__.'/routes/api.zermelo.php');

            $tabular_api_prefix = config('zermelo.TABULAR_API_PREFIX');
            Route::group( ['prefix' => $tabular_api_prefix ], function() {
                $this->loadRoutesFrom(__DIR__.'/routes/api.tabular.php');
            });

            $graph_api_prefix = config('zermelo.GRAPH_API_PREFIX');
            Route::group( ['prefix' => $graph_api_prefix ], function() {
                $this->loadRoutesFrom(__DIR__.'/routes/api.graph.php');
            });

            $tree_api_prefix = config('zermelo.TREE_API_PREFIX');
            Route::group( ['prefix' => $tree_api_prefix ], function() {
                $this->loadRoutesFrom(__DIR__.'/routes/api.tree.php');
            });

        });
    }

    /**
     * Get the Nova route group configuration array.
     *
     * @return array
     */
    protected function routeConfiguration()
    {
        $middleware = config('zermelo.MIDDLEWARE',[ 'api' ]);
        $middlewareString = implode(',', $middleware );

        return [
            'namespace' => 'CareSet\Zermelo\Http\Controllers',
            'domain' => config('zermelo.domain', null),
            'as' => 'zermelo.api.',
            'prefix' => config( 'zermelo.API_PREFIX' ),
            'middleware' => $middlewareString,
        ];
    }
}
