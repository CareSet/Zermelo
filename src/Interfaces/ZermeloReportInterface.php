<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 4/13/18
 * Time: 12:14 PM
 */

namespace CareSet\Zermelo\Interfaces;


interface ZermeloReportInterface
{
    public function pushViewVariable($name, $value);

    public function setToken($token);

    public function isSQLPrintEnabled();
}
