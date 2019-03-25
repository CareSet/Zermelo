<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 6/20/18
 * Time: 12:01 PM
 */

namespace CareSet\Zermelo\Models;

use Illuminate\Support\ServiceProvider;

abstract class AbstractZermeloProvider extends ServiceProvider
{
    abstract protected function onBeforeRegister();

    public function register()
    {
        $this->onBeforeRegister();

    }
}
