<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 6/20/18
 * Time: 12:01 PM
 */

namespace CareSet\Zermelo\Models;


use CareSet\Zermelo\Interfaces\ControllerInterface;
use Illuminate\Support\ServiceProvider;
use Mockery\Exception;

abstract class AbstractZermeloProvider extends ServiceProvider
{
    protected $controllers = [];

    public function register()
    {
        foreach ( $this->controllers as $controllerClass ) {
            $controller = $this->app->make( $controllerClass );
            if ( $controller instanceof ControllerInterface ) {
                $this->registerController( $controller );
            } else {
                throw new Exception( "$controllerClass cannot be made." );
            }

        }
    }

    public function registerController( ControllerInterface $controllerInterface )
    {
        // Get the singleton and register my route and presenters
        $controllerRepository = $this->app->make('CareSet\Zermelo\Models\ControllerRepository');
        $controllerRepository->add( $controllerInterface );
    }
}
