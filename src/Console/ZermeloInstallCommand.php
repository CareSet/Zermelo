<?php

namespace CareSet\Zermelo\Console;

use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use phpDocumentor\Reflection\Types\Static_;

class ZermeloInstallCommand extends AbstractZermeloInstallCommand
{
    /**
     * The views that need to be exported.
     *
     * @var array
     */
    public static $views = [
        // SQL Pretty-Printing views
        'zermelo/sql.blade.php',
        'zermelo/layouts/sql_layout.blade.php',
        // Card views
        'zermelo/card.blade.php',
        'zermelo/layouts/card_layout.blade.php',
        // Graph views
        'zermelo/d3graph.blade.php',
        'zermelo/layouts/d3graph_layout.blade.php',
        // Tabular views
        'zermelo/tabular.blade.php',
        'zermelo/layouts/tabular_layout.blade.php',
        // Tree-card views
        'zermelo/tree_card.blade.php',
        'zermelo/layouts/tree_card_layout.blade.php',
    ];

    /**
     * Base directory indicating where the $views are located
     *
     * @var string
     */
    protected static $view_path = __DIR__ . '/../../views';

    /**
     * Map of assets (CSS and Javascript) that need to be exported to public,
     * in the format 'source => target'
     *
     * @var string[]
     */
    protected static $assets = [
        // Core assets brought into vendor by composer
        // Bootstrap bundle contains Popper.js so we don't need to add it
        '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js' => '/core/bootstrap/bootstrap.bundle.min.js',
        '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css' => '/core/bootstrap/bootstrap.min.css',
        '/vendor/components/jquery/jquery.min.js' => '/core/js/jquery.min.js',
        '/vendor/moment/moment/min/moment.min.js' => '/core/js/moment.min.js',
        '/vendor/fortawesome/font-awesome/webfonts' => '/core/font-awesome/webfonts',
        '/vendor/fortawesome/font-awesome/css/all.min.css' => '/core/font-awesome/css/all.min.css',
        '/vendor/fortawesome/font-awesome/js/all.min.js' => '/core/font-awesome/js/all.min.js',

        // Core assets that live in the zermelo repo
        '/vendor/careset/zermelo/assets/core/css' => '/core/css',
        '/vendor/careset/zermelo/assets/core/js' => '/core/js',

        // Graph Assets
        '/vendor/careset/zermelo/assets/zermelobladegraph/css' => '/zermelobladegraph/css',
        '/vendor/careset/zermelo/assets/zermelobladegraph/js' => '/zermelobladegraph/js',

        // Tabular Assets
        '/vendor/careset/zermelo/assets/zermelobladetabular/datatables' => '/zermelobladetabular/datatables',
        '/vendor/careset/zermelo/assets/zermelobladetabular/js' => '/zermelobladetabular/js',
    ];

    protected static $config_file = __DIR__.'/../../config/zermelo.php';

    /**
     * @var string
     *
     * Console command signature
     */
    protected $signature = 'zermelo:install
                    {--database= : Pass in the database name}
                    {--force : Overwrite existing views and database by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all available Zermelo packages';

    const CONFIG_MIGRATIONS_PATH = 'vendor/careset/zermelo/database/migrations';

    public function handle()
    {
        // Tell the system that the installer is running
        Config::set('zermelo:install_api.running', true);

        $this->info("Creating directories....");
        $this->createDirectories();
        $this->info("Done.");

        $this->info("Exporting views....");
        $this->exportViews();
        $this->info("Done.");

        $this->info("exporting config....");
        if (!empty(static::$config_file)) {
            $this->exportConfig();
        }
        $this->info("Done.");

        $this->info("exporting assets....");
        $this->exportAssets();
        $this->info("Done.");

        if ($this->config_changes) {
            $path_parts = pathinfo(self::config_file);
            $user_config_file = $path_parts['basename'];
            $config_namespace = $path_parts['filename'];
            $array = Config::get($config_namespace);
            $data = var_export( $array, 1 );
            if (File::put(config_path($user_config_file), "<?php\n return $data;")) {
                $this->info( "Wrote new config file" );
            } else {
                $this->error("There were config changes, but there was an error writing config file.");
            }
        }

        // Install the Database, and core views
        $this->info("Installing Zermelo Database");
        $install_core = $this->installDatabase();

        $this->info("Installation Successful.");
    }

    protected function installDatabase()
    {
        $this->info("Setting up cache and config databases...");

        // If there are any config changes from the installation command, we track with this flag in case
        // we need to write an updated config file.
        $config_changes = false;

        $zermelo_cache_db_name = config( 'zermelo.ZERMELO_CACHE_DB' );
        $zermelo_config_db_name = config( 'zermelo.ZERMELO_CONFIG_DB' );

        // Check if our cache database exists, so we know whether to create it or not.
        try {
            $cache_db_exists = ZermeloDatabase::doesDatabaseExist($zermelo_cache_db_name);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            exit();
        }

        $create_zermelo_cache_db = true;
        if ($cache_db_exists === true &&
            ! $this->option('force') ) {

            if ( !$this->confirm("The Zermelo database '".$zermelo_cache_db_name."' already exists. Do you want to DROP it and recreate it?")) {
                $create_zermelo_cache_db = false;
            }
        }


        // See if the config database exists already. If we can't run the query (exception is thrown)
        // display the error message and exit.
        try {
            $config_db_exists = ZermeloDatabase::doesDatabaseExist($zermelo_config_db_name);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            exit();
        }

        //deleting the centralized configuration of wrenches and sockets that already exist in a database
        //would be a disaster. We should never overwrite a configuration database.
        //if someone wants a new one, they can create it themselves and then we will create it if it is missing..
        $create_zermelo_config_db = true;
        if ($config_db_exists === true) {
            $create_zermelo_config_db = false;
            $this->info("The database $zermelo_config_db_name already exists... using it");
        }

        $create_cache_failed = false;
        if ( $create_zermelo_cache_db ) {
            try {
                $this->info("Running intial cache migration...");
                $this->runZermeloInitialCacheMigration($zermelo_cache_db_name);
            } catch (\Exception $e) {
                $create_cache_failed = true;
            }
        }

        // The following block spits out an error message that indicates why Zermelo probably couldn't create
        // the cache database if the DB still doesn't exist after attempting to create it
        if ($create_cache_failed === true ||
            !ZermeloDatabase::doesDatabaseExist($zermelo_cache_db_name)) {
            $message = "Zermelo is unable to create the cache database,\n";
            $message .= "Please check the username and password in your .env file's database credentials and try again.\n";
            $default = config( 'database.default' );
            $username = config( "database.connections.$default.username" );
            $message .= "You are trying to connect with mysql user `$username`, you may have to run the following commands:\n";
            $message .= "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES ON `_zermelo_cache`.* TO '$username'@'localhost';\n";

            $this->error($message);
            exit();
        }

        $create_config_failed = false;
        // Do we need to create the config database, or do we migrate only?
        if ( $create_zermelo_config_db ) {
            $this->info("Running intial config migration...");
            try {
                $this->runZermeloInitialConfigMigration($zermelo_config_db_name);
            } catch (\Exception $e) {
                $create_config_failed = true;
            }
        } else {
            $this->info("Running update config migration...");
            $this->migrateDatabase( $zermelo_config_db_name, self::CONFIG_MIGRATIONS_PATH );
        }

        // The following block spits out an error message that indicates why Zermelo probably couldn't create
        // the config database if the DB still doesn't exist after attempting to create it
        if ($create_config_failed === true ||
            !ZermeloDatabase::doesDatabaseExist($zermelo_config_db_name)) {
            $message = "Zermelo is unable to create the config database,\n";
            $message .= "Please check the username and password in your .env file's database credentials and try again.\n";
            $default = config( 'database.default' );
            $username = config( "database.connections.$default.username" );
            $message .= "You are trying to connect with mysql user `$username`, you may have to run the following commands:\n";
            $message .= "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES ON `_zermelo_config`.* TO '$username'@'localhost';";

            $this->error($message);
            exit();
        }

        Artisan::call('zermelo:debug', [], $this->getOutput());

        $this->info("Done.");

        return true;
    }

    public function runZermeloInitialCacheMigration( $zermelo_cache_db_name )
    {
        // Create the database
        if ( ZermeloDatabase::doesDatabaseExist( $zermelo_cache_db_name ) ) {
            DB::connection()->statement( DB::connection()->raw( "DROP DATABASE IF EXISTS " . $zermelo_cache_db_name . ";" ) );
        }

        DB::statement("CREATE DATABASE IF NOT EXISTS `".$zermelo_cache_db_name."`;");

        // Write the database name to the master config
        config( ['zermelo.ZERMELO_CACHE_DB' => $zermelo_cache_db_name ] );

        // Configure the database for usage
        ZermeloDatabase::configure( $zermelo_cache_db_name );
    }

    public function runZermeloInitialConfigMigration( $zermelo_config_db_name )
    {
        // Create the database
        if ( ZermeloDatabase::doesDatabaseExist( $zermelo_config_db_name ) ) {
            DB::connection()->statement( DB::connection()->raw( "DROP DATABASE IF EXISTS " . $zermelo_config_db_name . ";" ) );
        }

        DB::statement("CREATE DATABASE IF NOT EXISTS `".$zermelo_config_db_name."`;");

        // Write the database name to the master config
        config( ['zermelo.ZERMELO_CONFIG_DB' => $zermelo_config_db_name ] );

        $this->migrateDatabase( $zermelo_config_db_name, self::CONFIG_MIGRATIONS_PATH );
    }

    public function migrateDatabase( $dbname, $path )
    {
        // unsure the database is configured for usage
        ZermeloDatabase::configure( $dbname );

        Artisan::call('migrate', [
            '--force' => true,
            '--database' => $dbname,
            '--path' => $path
        ]);
    }
}
