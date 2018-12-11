<?php

namespace CareSet\Zermelo\Console;

use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ZermeloInstallCommand extends AbstractZermeloInstallCommand
{
    protected $config_file = __DIR__.'/../config/zermelo.php';

    protected $signature = 'install:zermelo
                    {--database}
                    {--force : Overwrite existing views and database by default}';

    public function handle()
    {
        // Do view, config and asset installing first
        parent::handle();

        // If the user specifies a database name, user that, otherwise
        // use the default database name
        if ( $this->option( 'database' ) ) {
            $zermelo_db_name = $this->option( 'database' );
        } else {
            $zermelo_db_name = config( 'zermelo.ZERMELO_DB' );
        }

        $create_zermelo_db = true;
        if ( ZermeloDatabase::doesDatabaseExist( $zermelo_db_name ) &&
            ! $this->option('force') ) {

            if ( !$this->confirm("The Zermelo database '".$zermelo_db_name."' already exists. Do you want to DROP it and recreate it?")) {
                $create_zermelo_db = false;
            }
        }

        if ( $create_zermelo_db ) {
            $this->runZermeloMigration( $zermelo_db_name );
        }

        if ( ! $this->option('force') ) {
            if ( !$this->confirm("Would you like to use your previously installed Bootstrap CSS file?" )) {
                $bootstrap_css_location = $this->ask("Please paste the path of your bootstrap CSS file relative to public:");
                // Write the bootstrap CSS location to the master config
                config( ['zermelo.BOOTSTRAP_CSS_LOCATION' => $bootstrap_css_location ] );
            }
        }

        return true;
    }

    public function runZermeloMigration( $zermelo_db_name )
    {
        // Create the database
        if ( ZermeloDatabase::doesDatabaseExist( $zermelo_db_name ) ) {
            DB::connection()->statement( DB::connection()->raw( "DROP DATABASE IF EXISTS " . $zermelo_db_name . ";" ) );
        }

        DB::connection()->statement( DB::connection()->raw( "CREATE DATABASE `".$zermelo_db_name."`;" ) );

        // Write the database name to the master config
        config( ['zermelo.ZERMELO_DB' => $zermelo_db_name ] );

        // Configure the database for usage
        ZermeloDatabase::configure( $zermelo_db_name );

        Artisan::call('migrate', [
            '--force' => true,
            '--database' => $zermelo_db_name,
            '--path' => 'vendor/careset/zermelo/database/migrations'
        ]);
    }
}
