<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/6/18
 * Time: 2:26 PM
 */

namespace CareSet\Zermelo\Models;

class Wrench extends AbstractZermeloModel
{
    protected $table = 'wrench';

    public function sockets()
    {
        return $this->hasMany(Socket::class )->orderBy('socket_label');
    }
}
