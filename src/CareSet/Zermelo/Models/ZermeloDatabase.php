<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/7/18
 * Time: 9:19 AM
 *
 * This is a class of database helpers to perform common operations,
 * like dynamically configuring a database with Laravel, checking if
 * database exists, getting meta data, etc.
 */

namespace CareSet\Zermelo\Models;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ZermeloDatabase
{
    public static function configure( $database )
    {
        //
        $default = config( 'database.default' );
        Config::set( 'database.connections.'.$database, [
            'driver' => config( "database.connections.$default.driver" ),
            'host' => config( "database.connections.$default.host" ),
            'port' => config( "database.connections.$default.port" ),
            'database' => $database,
            'username' => config( "database.connections.$default.username" ),
            'password' => config( "database.connections.$default.password" ),
        ] );

        // Set the max concat length for cache DB to be A LOT
        DB::connection( $database )->statement( DB::raw( "SET SESSION group_concat_max_len = 1000000;" ) );
    }

    public static function hasTable( $table_name, $connectionName )
    {
        return Schema::connection( $connectionName )->hasTable( $table_name );
    }

    public static function drop( $table_name, $connectionName )
    {
        return Schema::connection( $connectionName )->drop( $table_name );
    }


    public static function connection($connectionName)
    {
        //
        return DB::connection( $connectionName );
    }

    public static function doesDatabaseExist( $database )
    {
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";

        // In case the database in the database.php or .env file doesn't exist, we can safely
        // set this to null so the select call will work, otherwise, we get a mysterious error
	$previous_mysql_database = config('database.connections.mysql.database');
//        config(["database.connections.mysql.database" => null]);

        try {
            $db = DB::select( $query, [ $database ] );
        } catch ( \Exception $e ) {
            $db = null;
        }

	//now that this is done, lets restore the previous database 
//       config(["database.connections.mysql.database" => $previous_mysql_database]);

        if ( empty( $db ) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * basicTypeFromNativeType
     * Simple way to determine the type of the column.
     * It can return: integer,decimal,string
     *
     * @param string $native
     * @return string
     */
    public static function basicTypeFromNativeType(string $native)
    {
        if (strpos($native, "int") !== false) {
            return "integer";
        }
        if (strpos($native, "double") !== false) {
            return "decimal";
        }
        if (strpos($native, "decimal") !== false || strpos($native, "float") !== false) {
            $reg = '/^(\w+)\((\d+?),(\d+)\)$/i';
            if (preg_match($reg, $native, $matches)) {
                $type = $matches[1];
                $len = $matches[2];
                $precision = $matches[3];
                if ($precision > 0) {
                    return "decimal";
                }

                return "integer";
            }
        }

        if (strpos($native, "varchar") !== false || strpos($native, "text") !== false) {
            return "string";
        }

        if ($native == "date" || $native == "time" || $native == "datetime") {
            return $native;
        }

        if ($native == "timestamp") {
            return "datetime";
        }

        return "string";
    }

    /**
     * getTableColumnDefinition
     * Get the column name and the basic column data type (integer, decimal, string)
     *
     * @return array
     */
    public static function getTableColumnDefinition( $table_name, $connectionName ): array
    {
        $result = self::connection($connectionName)->select("SHOW COLUMNS FROM {$table_name}");
        if ($result) {
            $column_meta = [];
            foreach ($result as $column) {
            $column_meta[$column->Field] = [
                    'Name' => $column->Field,
                    'Type' => self::basicTypeFromNativeType($column->Type),
                ];
            }
        } else {
            throw new \Exception("Could not execute `SHOW COLUMNS FROM {$table_name}`");
        }
        return $column_meta;
    }

    /**
     * isColumnInKeyArray
     * * Will take a column name and convert it into a word array to be passed to isWordInArray
     *
     * @param string $column_name
     * @param array $key_array
     * @return bool
     */
    public static function isColumnInKeyArray(string $column_name, array $key_array): bool
    {
        $column_name = strtoupper($column_name);
        /*
        Lets split the column name into 'words' and ucasing it
         */
        $words = ucwords(str_replace('_', ' ', $column_name), "\t\r\n\f\v ");
        $words = explode(" ", $words);

        $key_array = array_map('strtoupper', $key_array);
        if (in_array($column_name, $key_array)) {
            return true;
        }

        return self::isWordInArray($words, $key_array);
    }


    /**
     * isWordInArray
     * Determine if any word stub is inside a list of key words
     * Example: when $neddle is ['GROUP','ID'] and $haystack is ['ID'], then result will be true
     * This will also return true if $needle is ['GROUP','ID'] and the $haystack is ['GROUP_ID']
     *
     * @param array $needles
     * @param array $haystack
     * @return bool
     */
    protected static function isWordInArray(array $needles, array $haystack): bool
    {
        $full_needle = strtoupper(trim(implode(" ", $needles)));
        foreach ($haystack as $value) {
            $value = strtoupper($value);
            if (in_array($value, $needles) || $value == $full_needle) {
                return true;
            }

        }
        return false;
    }
}
