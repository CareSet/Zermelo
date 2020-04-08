<?php

namespace CareSet\Zermelo\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

abstract class AbstractZermeloInstallCommand extends Command
{

    protected $view_path = '';

    protected $asset_path = '';

    protected $config_file = '';

    /**
     * @var bool 
     * 
     * Set this variable to true if there are config changes, and the config
     * will write the new settings to your config file at the end of handle()
     */
    protected $config_changes = false;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("Creating directories....");
        $this->createDirectories();
        $this->info("Done.");

        $this->info("Exporting views....");
        $this->exportViews();
        $this->info("Done.");

        $this->info("exporting config....");
        $this->exportConfig();
        $this->info("Done.");

        $this->info("exporting assets....");
        $this->exportAssets();
        $this->info("Done.");
        
        if ($this->config_changes) {
            $path_parts = pathinfo($this->config_file);
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

        $this->info("Installation Successful.");
    }

    /**
     * Return true if two files are identical, return false OW
     *
     * @param $path_1
     * @param $path_2
     * @return bool
     *
     */
    public static function filesIdentical( $path_1, $path_2 )
    {
        if ( md5_file( $path_1 ) === md5_file( $path_2 ) ) {
            return true;
        }

        return false;
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->laravel['config']->get($key, []);

        $this->laravel['config']->set($key, array_merge(require $path, $config));
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (! is_dir($directory = report_path())) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = resource_path('views/zermelo'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = resource_path('views/zermelo/layouts'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the zermelo views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $value) {
            $source = $this->view_path.'/' .$value;
            $dest = resource_path('views/'.$value );
            if ( file_exists( $dest )
                && !$this->option('force') ) {

                // Don't copy, if we say no, don't even ask if they're identical
                if ( self::filesIdentical( $source, $dest ) ||
                    !$this->confirm("The [{$value}] view already exists. Do you want to replace it?") ) {
                    continue;
                }
            }

            copy(
                $source,
                $dest
            );
        }
    }

    protected function exportConfig()
    {
        $doCopy = true;
        $filename = basename( $this->config_file );

        if ( file_exists( config_path( $filename ) ) &&
            !$this->option('force') ) {

            // If the configs are identical, or if the user doesn't want to... don't overwrite
            if ( self::filesIdentical( $this->config_file, config_path( $filename )) ||
                !$this->confirm("The [{$filename}] config already exists. Do you want to replace it?")) {
                $doCopy = false;
            }
        }

        if ($doCopy) {
            copy(
                $this->config_file,
                config_path($filename)
            );

            $this->mergeConfigFrom(
                config_path($filename), basename($filename, ".php") // use the key as the filename without extension
            );
        } else {
            // Only if we are NOT copying the new config file, AND we have an old one
            // test to see if old/current config is missing any "new" settings from the package
            if (file_exists(config_path( $filename ))) {
                $newConfig = include $this->config_file;
                $currentConfig = include config_path($filename);
                foreach ($newConfig as $key => $value) {
                    if (!isset($currentConfig[$key])) {
                        $this->error("Your current configuration file is missing required setting `{$key}`. Please refer to the package config `{$this->config_file}` to copy the default setting value.");
                    }
                }
            }
        }
    }

    protected function exportAssets()
    {
        if ( !File::exists( public_path( 'vendor/CareSet' ) ) ) {
            File::makeDirectory( public_path( 'vendor/CareSet' ), 0755, true );
        }

        if ( $this->asset_path ) {
            $new_files = File::allFiles( $this->asset_path );
            $new_pathnames = [];
            foreach ( $new_files as $new_file ) {
                $relativePathname = $new_file->getRelativePathname();
                $new_pathnames[] = $relativePathname;

                if ( file_exists( public_path( 'vendor/CareSet' ) . '/' . $relativePathname ) &&
                    !$this->option( 'force' ) ) {

                    // If the file exists, and is identical, don't bother to ask, just skip.
                    if ( self::filesIdentical($this->asset_path . '/' . $relativePathname, public_path( 'vendor/CareSet' ) . '/' . $relativePathname) ||
                        !$this->confirm( "The [{$relativePathname}] asset already exists. Do you want to replace it?" ) ) {
                        continue;
                    }
                }

                $dirname = pathinfo( public_path( 'vendor/CareSet' ) . '/' . $relativePathname, PATHINFO_DIRNAME );
                if ( !File::exists( $dirname ) ) {
                    File::makeDirectory( $dirname, 0755, true );
                }

                // If we say yes, or we're running in "force" mode, copy asset
                copy(
                    $this->asset_path . '/' . $relativePathname,
                    public_path( 'vendor/CareSet' ) . '/' . $relativePathname
                );
            }
        }
    }
}
