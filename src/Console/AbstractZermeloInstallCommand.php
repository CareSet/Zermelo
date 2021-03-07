<?php

namespace CareSet\Zermelo\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

abstract class AbstractZermeloInstallCommand extends Command
{
    /**
     * The file names of the views that need to be exported.
     *
     * Needs to be public so service provider can has access
     *
     * @var array
     */
    public static $views = [];

    /**
     * @var string
     *
     * Path to the view directory in vendor that need to be exported to resources.
     * Used with $views (above) to determine full path to vendor views.
     */
    protected static $view_path = '';

    /**
     * @var string
     *
     * Source path of the assets in vendor that need to be exported to public
     * relative to assets in this repository's 'assets' directory
     */
    protected static $asset_path = '';

    /**
     * @var string
     *
     * Target path of where the asset_path will be placed. When this is called, we
     * wrap in public_path() helper, so we don't need to specify 'public' direcotry
     */
    protected static $asset_target_path = 'vendor/CareSet/zermelo';

    /**
     * @var string
     *
     * Name of config file (to copy from vendor into config directory)
     */
    protected static $config_file = '';

    /**
     * @var bool
     *
     * Set this variable to true if there are config changes, and the config
     * will write the new settings to your config file at the end of handle()
     *
     * This can be modified throughout the installation process, so not defined static
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
        if (file_exists($path_1) &&
            file_exists($path_2) &&
            md5_file( $path_1 ) === md5_file( $path_2 ) ) {
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
        foreach (static::$views as $value) {
            $source = static::$view_path.'/' .$value;
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
        $filename = basename( static::$config_file );

        if ( file_exists( config_path( $filename ) ) &&
            !$this->option('force') ) {

            // If the configs are identical, or if the user doesn't want to... don't overwrite
            if ( self::filesIdentical( static::$config_file, config_path( $filename )) ||
                !$this->confirm("The [{$filename}] config already exists. Do you want to replace it?")) {
                $doCopy = false;
            }
        }

        if ($doCopy) {
            copy(
                static::$config_file,
                config_path($filename)
            );

            $this->mergeConfigFrom(
                config_path($filename), basename($filename, ".php") // use the key as the filename without extension
            );
        } else {
            // Only if we are NOT copying the new config file, AND we have an old one
            // test to see if old/current config is missing any "new" settings from the package
            if (file_exists(config_path( $filename ))) {
                $newConfig = include static::$config_file;
                $currentConfig = include config_path($filename);
                foreach ($newConfig as $key => $value) {
                    if (!isset($currentConfig[$key])) {
			$config_file = self::$config_file;
                        $this->error("Your current configuration file is missing required setting `{$key}`. Please refer to the package config `$config_file` to copy the default setting value.");
                    }
                }
            }
        }
    }

    protected function exportAsset($asset_source_filename, $asset_target_filename)
    {
        $source_exists = file_exists($asset_source_filename);
        $target_exists = file_exists($asset_target_filename);
        if ($source_exists && $target_exists &&
            !$this->option('force')) {

            // If the file exists, and is identical, don't bother to ask, just skip.
            if (!file_exists($asset_target_filename) ||
                self::filesIdentical($asset_source_filename, $asset_target_filename) ||
                !$this->confirm("The asset `{$asset_target_filename}` already exists. Do you want to replace it?")) {
                return false;
            }
        } else {
            if (!$source_exists) {
                $this->info("`$asset_source_filename` may not exist");
                return false;
            }
        }

        $dirname = pathinfo($asset_target_filename, PATHINFO_DIRNAME);
        if (!File::exists($dirname)) {
            $this->info("Creating dir `$dirname`");
            File::makeDirectory($dirname, 0755, true);
        }

        // If we say yes, or we're running in "force" mode, copy asset
        // copy() returns true or false
        return copy(
            $asset_source_filename,
            $asset_target_filename
        );
    }

    protected function exportAssets()
    {
        $this->info("Installing Zermelo Assets");

        foreach (static::$assets as $source => $target) {
            $source_path = base_path() . $source;
            $target_path = public_path(self::$asset_target_path) . $target;
            // If the source is a directory, copy all the files in that directory
            // NOTE this routine does not recurse into subdirectories.
            if (is_dir($source)) {
                $new_files = File::allFiles($source_path);
                $new_pathnames = [];
                foreach ($new_files as $new_file) {
                    if (is_dir($new_file)) {
                        continue;
                    }
                    $relativePathname = $new_file->getRelativePathname();
                    $new_pathnames[] = $relativePathname;
                    $asset_target_filename = $target_path . '/' . $relativePathname;
                    $asset_source_filename = $source_path . '/' . $relativePathname;
                    $file_was_copied = $this->exportAsset($asset_source_filename, $asset_target_filename);
                }
            } else {
                $this->exportAsset($source_path, $target_path);
            }
        }
    }
}
