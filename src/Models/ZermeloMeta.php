<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/6/18
 * Time: 2:26 PM
 */

namespace CareSet\Zermelo\Models;

class ZermeloMeta extends AbstractZermeloModel
{
    protected $table = 'zermelo_meta';

    protected $fillable = ['key', 'meta_key', 'meta_value'];
}