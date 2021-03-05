<?php

namespace CareSet\ZermeloBladeCard\Console;

use CareSet\Zermelo\Console\AbstractZermeloInstallCommand;

class ZermeloBladeCardInstallCommand extends AbstractZermeloInstallCommand
{
    /**
     * The views that need to be exported.
     *
     * @var array
     */
    public static $views = [
        'zermelo/card.blade.php',
        'zermelo/layouts/card.blade.php',
    ];

    protected static $view_path = __DIR__.'/../../views';

    protected static $asset_path = __DIR__.'/../../assets';

    protected static $config_file = __DIR__.'/../../config/zermelobladecard.php';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zermelo:install_zermelobladecard
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Zermelo Card Tabular report view';
}
