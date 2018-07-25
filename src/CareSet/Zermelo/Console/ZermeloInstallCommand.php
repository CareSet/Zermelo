<?php

namespace CareSet\Zermelo\Console;

class ZermeloInstallCommand extends AbstractZermeloInstallCommand
{
    protected $config_file = __DIR__.'/../config/zermelo.php';

    protected $signature = 'install:zermelo
                    {--force : Overwrite existing views by default}';

}
