<?php

namespace CareSet\Zermelo\Models;

use Carbon\Carbon;
use CareSet\Zermelo\Exceptions\InvalidDatabaseTableException;
use CareSet\Zermelo\Interfaces\ZermeloReportInterface;
use Illuminate\Support\Facades\DB;

class DatabaseCache
{
    protected $exists = false;
    protected $doClearCache = false;
    protected $generatedThisRequest = false;
    protected $columns = [];
    protected $cache_table = null;
    protected $report = null;
    protected $key = null;
    protected $connectionName = null;

    public function __construct(ZermeloReport $report, $connectionName = null)
    {
        $this->report = $report;

        // by default use the zermelo cache DB
        if ($connectionName === null) {
            $this->connectionName = zermelo_cache_db();
        } else {
            $this->connectionName = $connectionName;
        }

        // If the report overrides the database cache source, use that connection instead
        $cacheDatabaseSource = $report->getCacheDatabaseSource();
        if ($cacheDatabaseSource !== null) {
            if (isset($cacheDatabaseSource['database']) &&
                isset($cacheDatabaseSource['table'])) {
                // We have all the required parameters to override the cache database,
                // so let's set this object up to do that.
                // In this case, we never regenerate the cache table
                $this->connectionName = $cacheDatabaseSource['database'];
                // The default cache table from the config file is configured by default, but
                // if we're overriding we have to explicitly configure the new cache DB for access
                try {
                    ZermeloDatabase::configure($this->connectionName);
                } catch (\Exception $e) {
                    throw new InvalidDatabaseTableException("You attempted to override the cache database with `{$this->connectionName}` but the database does not exist or you do not have permission to access it");
                }

                $this->key = $cacheDatabaseSource['table'];
                $this->cache_table = ZermeloDatabase::connection($this->connectionName)->table("{$this->key}");
            }
        } else {
            // Under normal operation, where no overriding of cache is taking place,
            // we generate the cache table name based on a random key
            $clear_cache = filter_var($report->getInput('clear_cache'), FILTER_VALIDATE_BOOLEAN) == true ? true : false;
            $this->setDoClearCache($clear_cache);

            // Generate the prefix, but make sure it's not longer than 32 chars
            $this->key = $this->keygen($this->report->getClassName());
            $this->cache_table = ZermeloDatabase::connection($this->connectionName)->table("{$this->key}");

            if ($this->exists() === false ||
                $report->isCacheEnabled() === false ||
                $this->getDoClearCache() == true ||
                $this->isCacheExpired() === true) {
                //if any of the above is true, then we need to re-run the create table.
                $this->createTable();
                $this->generatedThisRequest = true;
            }
        }

        // Get the column names from the cache/result table
        try {
            $this->columns = ZermeloDatabase::getTableColumnDefinition($this->getTableName(), $this->connectionName);
        } catch (\Exception $e) {
            throw new InvalidDatabaseTableException("You attempted access a table `{$this->getTableName()}` on database `{$this->connectionName}` but the table does not exist or you do not have permission to access it");
        }

        return true;
    }

    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /*
        This function generates the name of the cache table.
        It refers to the getDataIdentityKey() function on the report...
    */
    protected function keygen($prefix = "")
    {
        $key = $this->report->getDataIdentityKey($prefix);
        return $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getTable()
    {
        return $this->cache_table;
    }

    public function getTableName()
    {
        return $this->cache_table->from;
    }

    public function getReport()
    {
        return $this->report;
    }

    public function exists(): bool
    {
        $hasTable = ZermeloDatabase::hasTable($this->cache_table->from, $this->connectionName);
        return $hasTable;
    }

    public function setDoClearCache($doClearCache)
    {
        $this->doClearCache = $doClearCache;
    }

    public function getDoClearCache()
    {
        return $this->doClearCache;
    }

    public function isCacheExpired()
    {
        $expired = false;
        $now = Carbon::now();
        $nowTimestamp = $now->timestamp;
        $expiredTime = $this->getExpireTime();
        $expireTimestamp = Carbon::parse($expiredTime)->timestamp;
        if ($nowTimestamp > $expireTimestamp) {
            $expired = true;
        }

        return $expired;
    }

    public function MapRow(array $row, int $row_number)
    {
        return $this->report->MapRow($row, $row_number);
    }

    public function OverrideHeader(array &$format, array &$tags): void
    {
        $this->report->OverrideHeader($format, $tags);
    }

    /*
        getIndividualQueries() serves to ensure that the SQL returned by an individual report always takes the same structure
        inside the reporting engine.

        Basically, its job is to ensure that it returns an array of SQL singletons.


    */
    public function getIndividualQueries()
    {
        $sql = $this->report->GetSQL();

        if (!$sql) {
            return false;
        }

        $all_queries = [];
        if (!is_array($sql)) {
            // we must always return an array. If a report returns a single SQL statement, lets tuck it into an array with just one member
            $sql = [$sql];
        }

        /*
	It is possible for single sql text field to contain multiple SQL queries seperated by semicolons.
	But we really want an array with elements of single SQL statements
        break up each queries by semi colon,
        we will run each query separately
         */
        foreach ($sql as $query) {
            $query = explode(";", $query);
            foreach ($query as $single_query) {
                if (!empty(trim($single_query))) {
                    $all_queries[] = trim($single_query);
                }
            }
        }

        //this will always be an array with singleton SQL statements. or false.
        return $all_queries;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * If the table exists, drop it and create it from the queries
     * in the report.
     */
    public function createTable()
    {
        // Clone the cache table, to avoid query modifications that may affect future queries
        $temp_cache_table = clone $this->cache_table;

        //we are starting over, so if the table exists.. lets drop it.
        if ($this->exists()) {
            ZermeloDatabase::drop($temp_cache_table->from, $this->connectionName);
        }

        //now we will loop over all of the SQL queries that make up the report.

        $queries = $this->getIndividualQueries();

	if ($queries) {
    	    //just in case someone uses an associated array...
	    $indexed_queries = array_values($queries);
            foreach ($indexed_queries as $index => $query) {

                if (strpos(strtoupper($query), "SELECT", 0) === 0) {
                    if ($index == 0) {
                        //for the first query, we use a CREATE TABLE statement
                        ZermeloDatabase::connection($this->connectionName)->statement(DB::raw("CREATE TABLE {$temp_cache_table->from} AS {$query}"));
                    } else {
                        //for all subsequent queries we use INSERT INTO to merely add data to the table in question..
			try {
                        	ZermeloDatabase::connection($this->connectionName)->statement(DB::raw("INSERT INTO {$temp_cache_table->from} {$query}"));

			} catch(\Illuminate\Database\QueryException $ex){


				//these database errors deserve better human readable responses...
				//they are common problems with Zermelo reports..
				//so lets catch them and make sure that they are clear to end users..
				$messages_to_filter = [

				'Insert value list does not match column list:' =>
"Zermelo Error: SQL Column Number Mismatch. 
It looks like there was more than one SQL statement in this report, but the two reports did not have exactly the same number of columns... which they must for the reporting engine to work. 
The specific error message from the database was:
",
				"Data too long for column 'link_type'" =>
"Zermelo Error: The first link_type column needs to have the longest name. 
It should not be that way, but it is... 
The specific error message from the database was: 
",

				];


				$original_message = $ex->getMessage();

				foreach($messages_to_filter as $find_me => $say_me){

					if(strpos($original_message,$find_me) !== false){

						$new_message = $say_me . $original_message;
						throw new \Exception($new_message);

					}
				}

				//if we get here then it is an "original" SQL error message..
				//no new information to add here... lets just re throw the original error
				throw $ex;

			}

                    }
                } else {
                    //this allows us to database maintainance tasks using UPDATES etc.
                    //note that non-select statements are executed in the same order as they are provided in the contents of the returned SQL
                    ZermeloDatabase::connection($this->connectionName)->statement(DB::raw($query));
                }
            }
        } else {
            //the report returned 'false'.
            //we need to figure out how to handle this.
        }


        $table_string_to_replace = '{{_CACHE_TABLE_}}';

        $index_sql_array = $this->report->GetIndexSQL();

        if (is_null($index_sql_array)) {
            //then this report has not defined any indexes for the index table.
            //do nothing...
        } else {
            //lets loop over the index commands, which should have {{_CACHE_TABLE_}} in the place of any database.table name
            //and then replace that string with our temp table name, and then run those indexes.
            foreach ($index_sql_array as $this_index_sql_template) {
                if (strpos($this_index_sql_template, $table_string_to_replace) !== false) {
                    //then we have the table string... lets replace it.
                    $index_sql_command = str_replace($table_string_to_replace, $temp_cache_table->from, $this_index_sql_template);
                    //now lets run those index commands...
                    ZermeloDatabase::connection($this->connectionName)->statement(DB::raw($index_sql_command));
                } else {
                    throw new Exception("Zermelo Report Error: $this_index_sql_template was retrieved from GetIndexSql() but it did not contain $table_string_to_replace");
                }

            }

        }


    }

    /**
     *  Get the formatted time when this cache was last created
     *
     * @return false|string
     */
    public function getLastGenerated()
    {
        $stats = DB::select("SELECT CURRENT_TIMESTAMP, CREATE_TIME,
                                    TIMESTAMPDIFF(MINUTE,CREATE_TIME, CURRENT_TIMESTAMP) as age
                                FROM information_schema.tables WHERE table_schema=? and table_name = ?", [$this->connectionName, $this->getTableName()]);

        if (!$stats) {
            return true;
        }

        $tz = DB::select('SELECT TIME_FORMAT( TIMEDIFF(NOW(), UTC_TIMESTAMP), "%H:%i" ) as TZ;');

        $stats = $stats[0];

        $time = $stats->CREATE_TIME;
        $offset = $tz[0]->TZ;
        if ($offset == '00:00') {
            $offset = "+$offset";
        }

        $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $time, $offset);
        $carbonTime->setTimezone(config('app.timezone'));
        $lastGeneratedTime = $carbonTime->toDateTimeString();
        return $lastGeneratedTime;
    }

    public function getExpireTime()
    {
        $expireTime = false;
        if ($this->report->isCacheEnabled()) {

            $expireTimeCarbon = Carbon::parse($this->getLastGenerated())->addSeconds($this->report->howLongToCacheInSeconds());
            $expireTime = date('Y-m-d H:i:s', $expireTimeCarbon->timestamp);
        }

        return $expireTime;
    }

    public function getGeneratedThisRequest()
    {
        return $this->generatedThisRequest;
    }
}
