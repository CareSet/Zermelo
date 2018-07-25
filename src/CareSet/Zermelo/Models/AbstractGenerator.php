<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 7/5/18
 * Time: 2:38 PM
 */

namespace CareSet\Zermelo\Models;


use CareSet\Zermelo\Exceptions\InvalidDatabaseTableException;
use Illuminate\Support\Facades\DB;

class AbstractGenerator
{
    protected $_full_table = null;

    protected $_Table = null;
    protected $_columns = [];
    protected $_filters = [];

    public function init( array $params = null )
    {
        $database = $params['database']; // this is cache_db
        $table = $params['table'];
        $this->_database = $database;
        $this->_table = $table;

        $this->_full_table = "{$this->_database}.{$this->_table}";

        try {
            $this->_Table = DB::table("{$this->_full_table}");
        }
        catch(Exception $e)
        {
            throw new InvalidDatabaseTableException("Unable to access {$this->_full_table}.");
        }

        $this->_columns = $this->getTableColumnDefinition();
    }

    /**
     * basicTypeFromNativeType
     * Simple way to determine the type of the column.
     * It can return: integer,decimal,string
     *
     * @param string $native
     * @return string
     */
    protected static function basicTypeFromNativeType(string $native)
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

    public function addFilter(array $filters)
    {
        foreach($filters as $field=>$value)
        {
            if($field == '_')
            {
                $fields = $this->_columns;
                $this->_Table->where(function($q) use($fields,$value)
                {
                    foreach ($fields as $field) {
                        $field_name = $field['Name'];
                        $q->orWhere($field_name, 'LIKE', '%' . $value . '%');
                    }
                });
            } else
            {
                $this->_Table->Where($field,'LIKE','%'.$value.'%');
            }
        }
    }

    public function orderBy(array $orders)
    {
        foreach ($orders as $key=>$direction) {
            $this->_Table->orderBy($key, $direction);
        }
    }

    /**
     * getTableColumnDefinition
     * Get the column name and the basic column data type (integer, decimal, string)
     *
     * @return array
     */
    protected function getTableColumnDefinition(): array
    {
        $result = DB::select("SHOW COLUMNS FROM {$this->_full_table}");
        $column_meta = [];
        foreach ($result as $column) {
            $column_meta[$column->Field] = [
                'Name' => $column->Field,
                'Type' => self::basicTypeFromNativeType($column->Type),
            ];
        }
        return $column_meta;
    }

    public function cacheTo($destination_database, $destination_table)
    {
        $full_table = "{$destination_database}.{$destination_table}";

        $CacheQuery = clone $this->_Table;
        $sql = $CacheQuery->select("*")->toSql();
        $params = $CacheQuery->getBindings();

        DB::statement("DROP TABLE IF EXISTS {$full_table}");
        DB::statement("CREATE TEMPORARY TABLE {$full_table} AS {$sql};",$params);

        return true;
    }

}