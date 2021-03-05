<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/6/18
 * Time: 2:26 PM
 */

namespace CareSet\Zermelo\Models;


use Illuminate\Database\Eloquent\Model;

abstract class AbstractZermeloModel extends Model
{
    protected $connection = null;

    public function __construct( array $attributes = [] )
    {
        parent::__construct( $attributes );

        // We use the zermelo config DB for our "in-house" models
        $this->connection = zermelo_config_db();
    }
}