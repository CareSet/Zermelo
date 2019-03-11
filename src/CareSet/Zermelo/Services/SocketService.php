<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 3/11/19
 * Time: 10:35 AM
 */

namespace CareSet\Zermelo\Services;

use CareSet\Zermelo\Models\Socket;
use CareSet\Zermelo\Models\Wrench;

class SocketService
{
    public function fetchWrenchForKey( $key )
    {
        return new Socket();
    }
}
