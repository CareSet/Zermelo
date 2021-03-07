<?php

namespace CareSet\Zermelo\Console;

use CareSet\Zermelo\Console\AbstractZermeloInstallCommand;

class ZermeloBladeGraphInstallCommand extends AbstractZermeloInstallCommand
{
    public static $views = [
        'zermelo/d3graph.blade.php',
        'zermelo/layouts/d3graph_layout.blade.php',
    ];

    protected static $view_path = __DIR__.'/../../views';

    protected static $asset_path = '/zermelobladegraph';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zermelo:install_zermelobladegraph
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Zermelo Blade Graph report view';
}
