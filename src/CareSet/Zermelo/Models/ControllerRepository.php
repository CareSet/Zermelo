<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 6/20/18
 * Time: 1:17 PM
 */

namespace CareSet\Zermelo\Models;

use CareSet\Zermelo\Exceptions\ControllerNamespaceCollisionException;
use CareSet\Zermelo\Interfaces\ControllerInterface;
use Illuminate\Support\Facades\App;

class ControllerRepository
{
    protected $controllers = [];

    public function add( ControllerInterface $controllerInterface )
    {
        $prefix = $controllerInterface->prefix();
        if ( !isset( $this->controllers[$prefix] ) ) {
            $this->controllers[$prefix] = $controllerInterface;
        } else {
            $class = get_class($controllerInterface);
            if ( !App::runningInConsole() ) {
                // If running in console, the config file is not read properly first, so we need to ignore this exception.
                throw new ControllerNamespaceCollisionException( "The prefix {$prefix} is taken and can't be used in {$class}. " );
            }
        }
    }

    public function all()
    {
        return $this->controllers;
    }

    public function find( $prefix )
    {
        $controller = $this->controllers[$prefix];
        return $controller;
    }
}
