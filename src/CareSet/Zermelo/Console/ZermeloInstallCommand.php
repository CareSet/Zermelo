<?php

namespace CareSet\Zermelo\Console;

use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ZermeloInstallCommand extends AbstractZermeloInstallCommand
{
    /*
     * Automatically copy the zermelo.js library in assets/js
     */
    protected $asset_path = __DIR__.'/../assets';

    protected $config_file = __DIR__.'/../config/zermelo.php';

    protected $signature = 'zermelo:install_api
                    {--database= : Pass in the database name}
                    {--force : Overwrite existing views and database by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the backend Zermelo API';

    const CONFIG_MIGRATIONS_PATH = 'vendor/careset/zermelo/database/migrations';

    public function handle()
    {
        // Tell the system that the installer is running
        Config::set('zermelo:install_api.running', true);

        // Do view, config and asset installing first
        parent::handle();

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

        if ( ! $this->option('force') ) {
            if ( $this->confirm("Would you like to use your previously installed Bootstrap CSS file?" )) {
                $bootstrap_css_location = $this->ask("Please paste the path of your bootstrap CSS file relative to public");
                // Write the bootstrap CSS location to the master config
                config( [ 'zermelo.BOOTSTRAP_CSS_LOCATION' => $bootstrap_css_location ] );
                $config_changes = true;
            }
        }

        // Write the runtime config changes
        if ( $config_changes ) {
            $array = Config::get( 'zermelo' );
            $data = var_export( $array, 1 );
            if ( File::put( config_path( 'zermelo.php' ), "<?php\n return $data ;" ) ) {
                $this->info( "Wrote new config file" );
            } else {
                $this->error("There were config changes, but there was an error writing config file.");
            }
        }

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
