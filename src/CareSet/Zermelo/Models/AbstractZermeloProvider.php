<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 6/20/18
 * Time: 12:01 PM
 */

namespace CareSet\Zermelo\Models;

use CareSet\Zermelo\Console\AbstractZermeloInstallCommand;
use Illuminate\Support\ServiceProvider;

abstract class AbstractZermeloProvider extends ServiceProvider
{
    abstract protected function onBeforeRegister();

    public function register()
    {
        $this->onBeforeRegister();
    }

    /**
     * @param $class
     * @throws \Exception
     *
     * Given an AbstractZermelloInstallCommand class, check to see if the required
     * views are published to the resources directory. If they don't exist, throw
     * an exception
     */
    public static function ensureViewsExist($class)
    {
        // Only check this if we are running in the web
        if (php_sapi_name() !== 'cli') {

            // Make sure our class is a subclass of AbstractZermelloInstallCommand
            if (in_array(AbstractZermeloInstallCommand::class, class_parents($class))) {

                // Loop through required views, and if one doesn't exist in the app's resources directory, throw exception
                foreach ($class::$views as $view) {
                    $publishedViewPath = resource_path('views') . DIRECTORY_SEPARATOR . $view;
                    if (!file_exists($publishedViewPath)) {
                        throw new \Exception("You are missing view `$view` in your resources dierectory. You may need to run `php artisan zermelo:install` at the root of your project");
                    }
                }
            }
        }
    }
}
