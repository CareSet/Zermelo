<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 4/8/19
 * Time: 11:15 AM
 */

namespace CareSet\Zermelo\Models;


class ZermeloCacheDatabase extends ZermeloDatabase
{
    public static function hasTable( $table_name )
    {
        return Schema::connection( self::connectionName() )->hasTable( $table_name );
    }

    public static function drop( $table_name )
    {
        return Schema::connection( self::connectionName() )->drop( $table_name );
    }

    public static function connectionName()
    {
        $zermelo_db = config('zermelo.ZERMELO_DB' );
        return $zermelo_db;
    }

    public static function connection()
    {
        //
        return DB::connection( self::connectionName() );
    }
}