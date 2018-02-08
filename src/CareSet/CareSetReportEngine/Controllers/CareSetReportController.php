<?php

namespace CareSet\CareSetReportEngine\Controllers;

use CareSet\CareSetReportEngine\Models\CareSetReport;
use App\Http\Controllers\Controller;
use CareSet\CareSetReportEngine\Exceptions\InvalidHeaderFormatException;
use CareSet\CareSetReportEngine\Exceptions\InvalidHeaderTagException;
use CareSet\CareSetReportEngine\Exceptions\UnexpectedHeaderException;
use CareSet\CareSetReportEngine\Exceptions\UnexpectedMapRowException;
use Request;
use DB;

class CaresetReportController extends Controller
{

    const MAX_PAGING_LIMIT = 99999999999999;

    public function ReportDisplay(CareSetReport $Report)
    {

        $uri = "/" . ltrim(Request::path(), "/ ");

        $input_bolt = Request::get('data-option');
        if ($input_bolt == "") $input_bolt = false;
        $Report->SetBolt($input_bolt);
        $request_form_input = json_encode(Request::all());

        $view_data = [];
        $view_data['Report_Name'] = $Report->getReportName();
        $view_data['Report_Description'] = $Report->getReportDescription();
        $view_data['api_url'] = config("caresetreportengine.API_PATH") . $Report->getClassName();
        $view_data['summary_url'] = config("caresetreportengine.SUMMARY_PATH") . $Report->getClassName();
        $view_data['input_bolt'] = $input_bolt;
        $view_data['request_form_input'] = $request_form_input;
        $view_data['token'] = \Auth::guard()->user()->last_token;

        return view(config("caresetreportengine.TEMPLATE"), $view_data);

    }


    /**
     * GetHeaderSummary
     * Retrieve the column summary information for the header.
     * This will also pass the information through MapRow once to get the proper re-naming if any
     * as well as OverrideHeader
     *
     * @param CareSetReport $Report
     * @param bool $includeSummary
     * @return void
     */
    private function GetHeaderSummary(CareSetReport $Report, bool $includeSummary)
    {
        $report_name = trim($Report->getClassName());
        $Parameters = $Report->getParameters();
        $Code = $Report->getCode();

        $cache_key = md5($Report->getClassName() . "-" . $Code . "-" . $Report->GetBoltId() . "-" . implode("-", $Parameters));
        $cache_table_stub = "{$report_name}_{$cache_key}";
        $cache_table = config("caresetreportengine.CACHE_DB") . ".{$cache_table_stub}";

        DB::statement(DB::raw("CREATE DATABASE IF NOT EXISTS " . config("caresetreportengine.CACHE_DB") . ";"));
        DB::statement(DB::raw("SET SESSION group_concat_max_len = 1000000;"));


        /*
            Check to see if the cache table already exists, if it does not, create it
         */
        $exists = count(DB::SELECT("SELECT table_name FROM information_schema.tables WHERE table_schema=? and table_name = ?", [config("caresetreportengine.CACHE_DB"), $cache_table_stub])) > 0;
        $cacheable = config("caresetreportengine.CACHABLE");

        if (!$exists || !$cacheable)
            $this->CacheReport($cache_table, $Report);
        else if($exists && self::CheckUpdateCacheForReport($Report))
        {
                $this->CacheReport($cache_table, $Report);
        }



        $headers = [];            //this is used to store the headers before and after the column definition
        $mapped_header = [];      //this is the result from the MapRow function
        $original_array_key = []; //this is the original field name from the table

        $data_row = DB::table($cache_table)->first();
        $fields = self::getTableColumnDefinition($cache_table);
        

        //convert stdClass to array
        $data_row = json_decode(json_encode($data_row), true);
        $original_array_key = array_keys($data_row);

        /*
            Run the MapRow once to get the proper column name from the Report
         */
        $data_row = $Report->MapRow($data_row);
        $mapped_header = array_keys($data_row);


        /*
            This makes sure no new columns were added or removed.
         */
        if (count($original_array_key) != count($mapped_header)) {
            throw new UnexpectedMapRowException();
        }

        /*
            Converts the header into an key/value pair. the key being the column name.
            Call the OverrideHeader function from the Report to override any kind of header data.
         */
        $header_format = array_combine($mapped_header, array_fill(0, count($mapped_header), null));
        $header_tags = array_combine($mapped_header, array_fill(0, count($mapped_header), null));
        

        /*
            Determine the header format based on the column title and type
         */
        $header_format = $this->DefaultColumnFormat($Report,$header_format,$fields);

        /*
            Override the default header with what the report gives back,
            then check to see if the format and tags are valid
         */
        $Report->OverrideHeader($header_format,$header_tags);
        foreach($header_format as $name=>$format)
        {
            if(!in_array($name,$mapped_header))
                throw new UnexpectedHeaderException("Column header not found: {$name}");

            if($format!==null && !in_array($format,$Report->VALID_COLUMN_FORMAT))
                throw new InvalidHeaderFormatException("Invalid column header format: {$format}");
        }

        foreach($header_tags as $name=>&$tags)
        {
            if(!in_array($name,$mapped_header))
                throw new UnexpectedHeaderException("Column header not found: {$name}");

            if($tags == null)
                $tags = [];

            if(!is_array($tags))
                $tags = [$tags];
            

            if(config("caresetreportengine.RESTRICT_TAGS"))
            {
                $valid_tags = config("caresetreportengine.TAGS");

                foreach($tags as $tag)
                {
                    if(!in_array($tag,$valid_tags))
                    {
                        throw new InvalidHeaderTagException("Invalid tag: {$tag}");
                    }
                }
            }
        }

        /*
            Calculate the distinct count, sum, avg, std, min, max for fields that are integer/date base
         */
        $summary_data = [];
        if($includeSummary)
        {
            $target_fields = [];
            foreach ($fields as $field_name => $field) {
                if ($field['Type'] == 'string')
                {
                    $target_fields[] = "count(distinct(`{$field_name}`)) as `cnt_{$field_name}`";
                }
                else if ($field['Type'] == 'integer' || $field['Type'] == 'decimal')
                {
                    $target_fields[] = "sum(`{$field_name}`) as `sum_{$field_name}`";
                    $target_fields[] = "avg(`{$field_name}`) as `avg_{$field_name}`";
                    $target_fields[] = "std(`{$field_name}`) as `std_{$field_name}`";
                    $target_fields[] = "min(`{$field_name}`) as `min_{$field_name}`";
                    $target_fields[] = "max(`{$field_name}`) as `max_{$field_name}`";
                }
                else if ($field['Type'] == 'date') 
                {
                    $target_fields[] = "FROM_UNIXTIME(avg(UNIX_TIMESTAMP(`{$field_name}`))) as `avg_{$field_name}`";
                    $target_fields[] = "min(`{$field_name}`) as `min_{$field_name}`";
                    $target_fields[] = "max(`{$field_name}`) as `max_{$field_name}`";
                }
            }
            $target_fields = implode(",", $target_fields);
            $result = json_decode(json_encode(DB::table($cache_table)->selectRaw($target_fields)->first()), true);

            /*
                Parse the result out into an associated array with the proper field name as the key
            */
            foreach ($result as $col => $value) {
                $reg = '/^(cnt|sum|avg|std|min|max)_(.*)$/i';
                if (preg_match($reg, $col, $matches)) {
                    $summary_type = $matches[1];
                    $column_name = $matches[2];
                    static $type_value = [
                        "cnt" => "count",
                        "sum" => "sum",
                        "avg" => "average",
                        "std" => "standard_deviation",
                        "min" => "minimum",
                        "max" => "maximum"
                    ];
                    $summary_data[$column_name][$type_value[$summary_type]] = $value;
                }
            }

            /*
                Check if any column are in the SUGGEST_NO_SUMMARY and add a flag
            */
            foreach($summary_data as $name=>$data)
            {
                if (self::isColumnInKeyArray($name, $Report->SUGGEST_NO_SUMMARY))
                {
                    $summary_data[$name]['NO_SUMMARY'] = true;
                }
            }
        }

        /*
            Merge format/tags/summary information together into 1 array
        */
        $header = [];
        foreach($header_format as $name=>$field)
        {
            $title = ucwords(str_replace('_', ' ', $name), "\t\r\n\f\v ");
            $column = [
                'field' => $name,
                'title' => $title,
                'format' => $header_format[$name] ?? 'TEXT',
                'tags'   => $header_tags[$name] ?? []
            ];

            if(key_exists($name,$summary_data))
                $column['summary'] = $summary_data[$name];

            $header[] = $column;
        }


        return $header;
    }






    public function ReportModelSummaryJson(CareSetReport $Report, $show_summary = true)
    {
        $input_bolt = Request::get('data-option');
        return [
            'Report_Name' => $Report->getReportName(),
            'Report_Description' => $Report->getReportDescription(),
            'selected-data-option' => $input_bolt,
            'graphable' => self::IsReportGraphable($Report),
            'columns' => $this->GetHeaderSummary($Report,$show_summary)
        ];
    }



    /**
     * ReportModelJson
     * Return the CareSetReport as a pagable model
     *
     * @param CareSetReport $Report
     * @param string $Code
     * @param array $Parameters
     * @return json paginated
     */
    public function ReportModelJson(CareSetReport $Report)
    {
        $input_bolt = Request::get('data-option');
        $report_name = trim($Report->getClassName());
        $Code = $Report->getCode();
        $Parameters = $Report->getParameters();

        $cache_key = md5($Report->getClassName() . "-" . $Code . "-" . $Report->GetBoltId() . "-" . implode("-", $Parameters));
        $cache_table_stub = "{$report_name}_{$cache_key}";
        $cache_table = config("caresetreportengine.CACHE_DB") . ".{$cache_table_stub}";

        $orderBy = Request::get('order') ?? [];
        $filter = Request::get('filter');

        $paging_length = Request::get("length") ?? 1000;
        if ($paging_length > 500000 && $paging_length > 0) $paging_length = 500000;
        if ($paging_length <= 0) $paging_length = self::MAX_PAGING_LIMIT; /* no limit*/

        DB::statement(DB::raw("CREATE DATABASE IF NOT EXISTS " . config("caresetreportengine.CACHE_DB") . ";"));
        DB::statement(DB::raw("SET SESSION group_concat_max_len = 1000000;"));

        $exists = count(DB::SELECT("SELECT table_name FROM information_schema.tables WHERE table_schema=? and table_name = ?", [config("caresetreportengine.CACHE_DB"), $cache_table_stub])) > 0;
        $cacheable = config("caresetreportengine.CACHABLE");

        if (!$exists || !$cacheable)
            $this->CacheReport($cache_table, $Report);
        else if($exists && self::CheckUpdateCacheForReport($Report))
        {
            $this->CacheReport($cache_table, $Report);
        }

        $all_rows = [];
        $headers = [];

        $paging = DB::table($cache_table);


        /*
            If there is a filter, lets apply it to each column
        */
        if($filter)
        {
            $fields = self::getTableColumnDefinition($cache_table);
            foreach($fields as $field)
            {
                $field_name = $field['Name'];
                $paging->orWhere($field_name,'LIKE','%'.$filter.'%');
            }
        }
        

        foreach ($orderBy as $order) {
            $orderKey = key($order);
            $direction = $order[$orderKey];
            $paging->orderBy($orderKey, $direction);
        }
        $paging = $paging->paginate($paging_length);


        /*
            Transform each row using $Report->MapRow()
        */
        $paging->getCollection()->transform(function($value) use($Report) {
            $value_array = json_decode(json_encode($value),true);
            return json_decode(json_encode($Report->MapRow($value_array)));
        });

        /*
            Add in the report name/description/columns
        */
        $custom = collect($this->ReportModelSummaryJson($Report,false));

        $merge = $custom->merge($paging);

        /*
            This sets the per_page size to 0 so it does not show the MAX_PAGING_LIMIT number
        */
        if($paging_length == self::MAX_PAGING_LIMIT)
            $merge['per_page'] = 0;
        
        return $merge;
    }

    /**
     * CachedReport
     * This takes a CareSetReport and create a cache table inside $cache_table with the result.
     *
     * @param string $cache_table       Hash destination table where the result will be stored
     * @param CareSetReport $Report     Report to run the queries from
     * @param string $Code              CareSetCode to target the Report
     * @param array $Parameters         Additional Get Parameters from the URI
     * @return void
     */
    public function CacheReport($cache_table, CareSetReport $Report)
    {

        $input_bolt = Request::get('data-option');
        if ($input_bolt == "") $input_bolt = false;
        $Report->SetBolt($input_bolt);
        $request_form_input = json_encode(Request::all());

        $sql = $Report->getSQL();

        if (!$sql)
            return false;

        $all_queries = [];
        if (!is_array($sql)) $sql = [$sql];
        

        /*
            break up each queries by semi colon,
            we will run each query separately
         */
        foreach ($sql as $query) {
            $query = explode(";", $query);
            foreach ($query as $single_query)
                if (!empty(trim($single_query)))
                $all_queries[] = trim($single_query);
        }


        /*
            On first run,
            we need to create the table instead of inserting into the table,
            if not cachable, then create a temporary table.
         */
        $first_loop = true;

        foreach ($all_queries as $s) {
            $s = trim($s);

            if (strpos(strtoupper($s), "SELECT", 0) === 0)
            {
                if ($first_loop) {
                    $first_loop = false;

                    DB::statement(DB::raw("DROP TABLE IF EXISTS {$cache_table}"));
                    DB::statement(DB::raw("CREATE TABLE {$cache_table} AS {$s}"));
                } else {
                    DB::statement(DB::raw("INSERT INTO {$cache_table} {$s}"));
                }
            } else {
                DB::statement(DB::raw($s));
            }
        }


        /*
            Lets try to be clever and attempt to index any 'subject' we have on the table.
        */
        if(config("caresetreportengine.AUTO_INDEX"))
        {
            $data_row = DB::table($cache_table)->first();
            if($data_row)
            {
                $data_row = json_decode(json_encode($data_row),true);
                $columns = array_keys($data_row);

                $to_index = [];
                foreach($columns as $column)
                {
                    if(self::isColumnInKeyArray($column, $Report->SUBJECTS))
                    {
                        $to_index[] = "ADD INDEX(`{$column}`)";
                    }
                }
                if(!empty($to_index))
                {
                    $to_index = "ALTER TABLE {$cache_table} ".implode(",",$to_index).";";
                    DB::statement($to_index);
                }
                
            }
        }

    }





    public function DefaultColumnFormat(CareSetReport $Report, array $format, array $fields): array
    {
        foreach ($format as $name => $value) {

            if (self::isColumnInKeyArray($name, $Report->DETAIL))
                $format[$name] = 'DETAIL';

            else if (self::isColumnInKeyArray($name, $Report->URL) && in_array($fields[$name]["Type"],["string"]))
                $format[$name] = 'URL';

            else if (self::isColumnInKeyArray($name, $Report->CURRENCY) && in_array($fields[$name]["Type"],["integer","decimal"]))
                $format[$name] = 'CURRENCY';

            else if (self::isColumnInKeyArray($name, $Report->NUMBER) && in_array($fields[$name]["Type"],["integer","decimal"]))
                $format[$name] = 'NUMBER';

            else if (self::isColumnInKeyArray($name, $Report->DECIMAL) && in_array($fields[$name]["Type"],["integer","decimal"]))
                $format[$name] = 'DECIMAL';

            else if (in_array($fields[$name]["Type"],["date","time","datetime"]))
                $format[$name] = strtoupper($fields[$name]["Type"]);

            else if (self::isColumnInKeyArray($name, $Report->PERCENT) && in_array($fields[$name]["Type"],["integer","decimal"]))
                $format[$name] = 'PERCENT';
        }

        return $format;
    }


    protected static function IsReportGraphable(CareSetReport $Report)
    {
        $Parameters = $Report->getParameters();
        $Code = $Report->getCode();
        $report_name = $Report->getClassName();

        $cache_key = md5($Report->getClassName() . "-" . $Code . "-" . $Report->GetBoltId() . "-" . implode("-", $Parameters));
        $cache_table_stub = "{$report_name}_{$cache_key}";
        $cache_table = config("caresetreportengine.CACHE_DB") . ".{$cache_table_stub}";

        $fields = self::getTableColumnDefinition($cache_table);


        foreach($fields as $field)
        {
            $column = $field['Name'];
            if(self::isColumnInKeyArray($column, $Report->SUBJECTS))
            {
                return true;
            }
            return false;
        }


    }

    protected static function CheckUpdateCacheForReport(CareSetReport $Report)
    {
        $Parameters = $Report->getParameters();
        $Code = $Report->getCode();
        $report_name = $Report->getClassName();

        $cache_key = md5($Report->getClassName() . "-" . $Code . "-" . $Report->GetBoltId() . "-" . implode("-", $Parameters));
        $cache_table_stub = "{$report_name}_{$cache_key}";


        /*
            Check to see if the report is pass its age, but only if the option is enabled
        */
        if(config("caresetreportengine.CACHE_TIMEOUT")*1 > 0)
        {
            $stats = DB::SELECT("SELECT CURRENT_TIMESTAMP, CREATE_TIME, 
                                    TIMESTAMPDIFF(MINUTE,CREATE_TIME, CURRENT_TIMESTAMP) as age 
                                FROM information_schema.tables WHERE table_schema=? and table_name = ?", [config("caresetreportengine.CACHE_DB"), $cache_table_stub]);
            
            if(!$stats) return true;

            $stats = $stats[0];

            $age = $stats->age;
            if($age > config("caresetreportengine.CACHE_TIMEOUT")*1)
            {
                return true;
            }
        }


        /*
            Check to see if the report file has been updated since the caching as occured.
            This is get the UTC time
        */
        $modified_at = new \DateTime();
        $modified_at = $modified_at->setTimestamp(filemtime($Report->getFileLocation()));
        $modified_at_utc_iso = $modified_at->format("Y-m-d H:i:s");


        $result = DB::SELECT("select
                    ? > (CONVERT_TZ(UPDATE_TIME, @@session.time_zone, '+00:00') ) as cache_outdated
                    FROM information_schema.tables WHERE table_schema = ? AND table_name = ?",[$modified_at_utc_iso,config("caresetreportengine.CACHE_DB"), $cache_table_stub]);

        if(!$result) return true;
        $result = $result[0]->cache_outdated;

        return $result == 1;
    }


    /**
     * isColumnInKeyArray
     * Will take a column name and convert it into a word array to be passed to isWordInArray
     * 
     * 
     */
    protected static function isColumnInKeyArray($column_name, $key_array) :bool
    {
        /*
            Lets split the column name into 'words' and ucasing it
        */
        $words = ucwords(str_replace('_', ' ', $column_name), "\t\r\n\f\v ");
        $words = explode(" ", strtoupper($words));

        return self::isWordInArray($words,$key_array);
    }

    /**
     * isWordInArray
     * Determine if any word stub is inside a list of key words
     * Example: when $neddle is ['GROUP','ID'] and $haystack is ['ID'], then result will be true
     * This will also return true if $needle is ['GROUP','ID'] and the $haystack is ['GROUP_ID']
     * 
     */
    protected static function isWordInArray(array $needles, array $haystack) :bool
    {
        $full_needle = strtoupper(trim(implode(" ", $needles)));
        foreach ($haystack as $value) {
            $value = strtoupper($value);
            if (in_array($value, $needles) || $value == $full_needle)
                return true;
        }
        return false;
    }


    /**
     * getTableColumnDefinition
     * Get the column name and the basic column data type (integer, decimal, string)
     * 
     * @param string $table
     * @return array
     */
    protected static function getTableColumnDefinition($table)
    {
        $result = DB::select("SHOW COLUMNS FROM {$table}");
        $column_meta = [];
        foreach ($result as $column) {
            $column_meta[$column->Field] = [
                'Name' => $column->Field,
                'Type' => self::basicTypeFromNativeType($column->Type)
            ];
        }
        return $column_meta;
    }

    /**
     * basicTypeFromNativeType
     * Simple way to determine the type of the column.
     * It can return: integer,decimal,string
     *
     * @param string $native
     * @return string
     */
    protected static function basicTypeFromNativeType($native)
    {
        if (strpos($native, "int") !== false) {
            return "integer";
        }

        if (strpos($native, "decimal") !== false || strpos($native, "float") !== false) {
            $reg = '/^(\w+)\((\d+?),(\d+)\)$/i';
            if (preg_match($reg, $native, $matches)) {
                $type = $matches[1];
                $len = $matches[2];
                $precision = $matches[3];
                if ($precision > 0)
                    return "decimal";
                return "integer";
            }
        }

        if (strpos($native, "varchar") !== false || strpos($native, "text") !== false) {
            return "string";
        }

        if($native=="date" || $native=="time" || $native=="datetime")
            return $native;
        
        if($native=="timestamp")
            return "datetime";

        return "string";
    }


}
