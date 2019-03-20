<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/6/18
 * Time: 2:26 PM
 */

namespace CareSet\Zermelo\Models;

class Socket extends AbstractZermeloModel
{
    protected $table = 'socket';

    public function wrench()
    {
        return $this->belongsTo( Wrench::class );
    }
}
