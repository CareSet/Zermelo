<?php

namespace CareSet\ZermeloBladeTabular\Console;

use CareSet\Zermelo\Console\AbstractZermeloInstallCommand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class ZermeloBladeTabularInstallCommand extends AbstractZermeloInstallCommand
{
    /**
     * The views that need to be exported.
     *
     * @var array
     */
    public static $views = [
        'zermelo/tabular.blade.php',
        'zermelo/layouts/tabular.blade.php',
    ];

    protected static $view_path = __DIR__.'/../../views';

    protected static $asset_path = __DIR__.'/../../assets';

    protected static $config_file = __DIR__.'/../../config/zermelobladetabular.php';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zermelo:install_zermelobladetabular
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Zermelo Blade Tabular report view';

    protected function exportAssets()
    {
        parent::exportAssets(); // Call parent export assets/

        $config_changes = false;
        if ( ! $this->option('force') ) {
            if ( $this->confirm("Would you like to use your previously installed Bootstrap CSS file, and specify it's path?" )) {
                $bootstrap_css_location = $this->ask("Please paste the path of your bootstrap CSS file relative to public");
                // Write the bootstrap CSS location to the master config
                config( [ 'zermelobladetabular.BOOTSTRAP_CSS_LOCATION' => $bootstrap_css_location ] );
                $this->config_changes = true;
            }
        }
    }
}
