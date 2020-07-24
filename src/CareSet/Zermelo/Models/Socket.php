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

    protected $fillable = ['wrench_id', 'socket_value', 'socket_label', 'is_default_socket', 'socketsource_id'];

    public function wrench()
    {
        return $this->belongsTo( Wrench::class );
    }
}
