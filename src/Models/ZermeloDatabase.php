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


use Illuminate\Database\QueryException;
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
        try {
            return DB::connection($connectionName);
        } catch(\Exception $e) {
            $message = $e->getMessage()." You may have a permissions error with your database user. Please Refer to the Zermelo troubleshooting guide <a href='https://github.com/CareSet/Zermelo#troubleshooting'>https://github.com/CareSet/Zermelo#troubleshooting</a>";
            throw new \Exception($message, $e->getCode(), $e);
        }
    }

    /**
     * @param $database
     * @return bool|null
     * @throws \Exception
     *
     * Returns true if DB exists, and false if it does not, NULL if the state of existence cannot be determined.
     */
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

            if ($e->getCode() == 1049) {
                // If the database in our configuration file doesn't exist, we have a problem,
                // So let's blow up.
                throw new \Exception($e->getMessage()."\n\nPlease make sure the database in your .env file exists.", $e->getCode());
            } else if ($e->getCode() == 1045) {
                // If the user doens't have authorization, we have a problem.
                $default = config( 'database.default' ); // Get default connection
                $username = config( "database.connections.$default.username" ); // Get username for default connection
                $message = "\n\nPlease check your user credentials and permissions and try again. Here are some suggestions:";
                $message .= "\n* `$username` may not exist.";
                $message .= "\n* `$username` may have the incorrect password in your .env file.";
                throw new \Exception($e->getMessage().$message, $e->getCode());
            } else if ($e->getCode() == 1044) {
                // Access Denied
                $default = config( 'database.default' ); // Get default connection
                $default_db = config( "database.connections.$default.database" );
                $username = config( "database.connections.$default.username" ); // Get username for default connection
                $message = "\n\nPlease make sure that your mysql user in your .env file has permissions on `$default_db`.";
                $message .= "\n* Run this mysql command to list users who have access:\n";
                $message .= "\tSELECT user from mysql.db where db='$default_db';"; // SHOW GRANTS FOR ken@localhost;;
                $message .= "\n* `$username` may have insufficient permissions and you may have to run the following command:\n";
                $message .= "\tGRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES ON `$default_db`.* TO '$username'@'localhost';\n";
                throw new \Exception($e->getMessage().$message, $e->getCode());
            }

            $db = null;
        }

	//now that this is done, lets restore the previous database 
//       config(["database.connections.mysql.database" => $previous_mysql_database]);


        // The DB exists if the schema name in the query matches our database
        $db_exists = false;
        if ( is_array($db) &&
            isset($db[0]) &&
            $db[0]->SCHEMA_NAME == $database) {
            $db_exists = true;
        } else {
            // Let's make sure that the database REALLY doesn't exist, not that we just don't have permission to see
            try {
                $query = "CREATE DATABASE IF NOT EXISTS `$database`;";
                DB::statement( $query );
            } catch ( \Exception $e ) {
                $default = config( 'database.default' ); // Get default connection
                $username = config( "database.connections.$default.username" ); // Get username for default connection
                $message = "\n\nYou may not have permission to the database `$database` to query its existence.";
                $message .= "\n* `$username` may have insufficient permissions and you may have to run the following command:\n";
                $message .= "\tGRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES ON `$database`.* TO '$username'@'localhost';\n";
                throw new \Exception($e->getMessage().$message, $e->getCode());
            }
        }

        if ($db_exists) {
            return true;
        } else if ($db_exists === false) {
            return false;
        } else if ($db == null) {
            return null;
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
