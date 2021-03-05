<?php

namespace CareSet\ZermeloBladeTreeCard\Console;

use CareSet\Zermelo\Console\AbstractZermeloInstallCommand;

class ZermeloBladeTreeCardInstallCommand extends AbstractZermeloInstallCommand
{
    /**
     * The views that need to be exported.
     *
     * @var array
     */
    public static $views = [
        'zermelo/tree_card.blade.php',
        'zermelo/layouts/tree_card.blade.php',
    ];

    protected static $view_path = __DIR__.'/../../views';

    protected static $asset_path = __DIR__.'/../../assets';

    protected static $config_file = __DIR__.'/../../config/zermelobladetreecard.php';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zermelo:install_zermelobladetreecard
                    {--force : Overwrite existing views by default}';
}
