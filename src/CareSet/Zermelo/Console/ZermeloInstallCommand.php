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

    protected $signature = 'install:zermelo_api
                    {--database= : Pass in the database name}
                    {--force : Overwrite existing views and database by default}';

    const CONFIG_MIGRATIONS_PATH = 'vendor/careset/zermelo/database/migrations';

    public function handle()
    {
        // Do view, config and asset installing first
        parent::handle();

        // If there are any config changes from the installation command, we track with this flag in case
        // we need to write an updated config file.
        $config_changes = false;

        $zermelo_cache_db_name = config( 'zermelo.ZERMELO_CACHE_DB' );
        $zermelo_config_db_name = config( 'zermelo.ZERMELO_CONFIG_DB' );

        $create_zermelo_cache_db = true;
        if ( ZermeloDatabase::doesDatabaseExist( $zermelo_cache_db_name ) &&
            ! $this->option('force') ) {

            if ( !$this->confirm("The Zermelo database '".$zermelo_cache_db_name."' already exists. Do you want to DROP it and recreate it?")) {
                $create_zermelo_cache_db = false;
            }
        }

        $create_zermelo_config_db = true;
        if ( ZermeloDatabase::doesDatabaseExist( $zermelo_config_db_name ) &&
            ! $this->option('force') ) {

            if ( !$this->confirm("The Zermelo database '".$zermelo_config_db_name."' already exists. Do you want to DROP it and recreate it?")) {
                $create_zermelo_config_db = false;
            }
        }

        if ( $create_zermelo_cache_db ) {
            $this->runZermeloInitialCacheMigration( $zermelo_cache_db_name );
        }

        // Do we need to create the cache database, or do we migrate only?
        if ( $create_zermelo_config_db ) {
            $this->runZermeloInitialConfigMigration( $zermelo_config_db_name );
        } else {
            $this->migrateDatabase( $zermelo_config_db_name, self::CONFIG_MIGRATIONS_PATH );
        }

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

        DB::connection()->statement( DB::connection()->raw( "CREATE DATABASE `".$zermelo_cache_db_name."`;" ) );

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

        DB::connection()->statement( DB::connection()->raw( "CREATE DATABASE `".$zermelo_config_db_name."`;" ) );

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
