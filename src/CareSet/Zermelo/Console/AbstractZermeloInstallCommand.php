<?php

namespace CareSet\Zermelo\Console;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Facades\File;

abstract class AbstractZermeloInstallCommand extends Command
{
    use DetectsApplicationNamespace;

    protected $view_path = '';

    protected $asset_path = '';

    protected $config_file = '';

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
        $this->createDirectories();

        $this->exportViews();

        $this->exportConfig();

        $this->exportAssets();
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
        $filename = basename( $this->config_file );

        if ( file_exists( config_path( $filename ) ) &&
            !$this->option('force') ) {

            // If the configs are identical, or if the user doesn't want to... don't overwrite
            if ( self::filesIdentical( $this->config_file, config_path( $filename )) ||
                !$this->confirm("The [{$filename}] config already exists. Do you want to replace it?")) {
                return;
            }
        }

        copy(
            $this->config_file,
            config_path( $filename )
        );
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
