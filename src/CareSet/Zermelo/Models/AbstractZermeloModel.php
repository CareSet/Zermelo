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

        $this->connection = zermelo_db();
    }
}