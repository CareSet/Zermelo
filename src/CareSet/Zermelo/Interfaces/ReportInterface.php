<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 4/13/18
 * Time: 12:14 PM
 */

namespace CareSet\Zermelo\Interfaces;


interface ReportInterface
{
    public function getApiPrefix() : string;

    public function setApiPrefix( string $api_prefix );


}