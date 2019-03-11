<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/6/18
 * Time: 2:26 PM
 */

namespace CareSet\Zermelo\Models;

use Illuminate\Database\Eloquent\Model;

class Wrench extends Model
{
    protected $table = 'wrench';

    public function sockets()
    {
        return $this->hasMany('socket');
    }
}
