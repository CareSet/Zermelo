<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/24/18
 * Time: 2:47 PM
 */

namespace CareSet\Zermelo\Reports\Tree;


use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloReport;
use CareSet\Zermelo\Models\ZermeloDatabase;
use \DB;

class CachedTreeReport extends DatabaseCache
{
    protected $cache_db = '_zermelo_cache';
    protected $tree_table = 'to be overwritten';

    /**
     * CachedGraphReport constructor.
     *
     * @param ZermeloReport $report The report to be cached
     *
     * @param $connectionName The name of the Cache Database connection, which represents the cache database name, and credentials for connecting
     */
    public function __construct(AbstractTreeReport $report, $connectionName)
    {

        // create cache tables, the logic in handled in the superclass constructor, and it only generates new table if required
        // If we are rebuilding the cache in this request, the parent will generate a table with the results from the report's
        // GetSQL() function query. Then, we generate the auxillary graph cache tables below
        parent::__construct($report, $connectionName);

        // Get our cache key from parent, and use it to name all of our auxiliary graph tables
        $cache_table_name_key = $this->getKey();
        $this->tree_table = "nodes_$cache_table_name_key";

	//TODO this should come from configuration if the configuration is set...
	$this->cache_db = '_zermelo_cache'; 

        // Only generate the aux tables (drop and re-create) if dictated by cache rules
        if ($this->getGeneratedThisRequest() === true) {
            $this->createTreeTables();
        }
    }

    private function createTreeTables()
    {
        $start_time = microtime(true);
        $sql = [];

    //    $sql['delete current tree table'] = "DROP TABLE IF EXISTS $this-$this->tree_table;";

	//this is work needed to be done to prep the cache to serve our specific report, beyond just merging all of the GetSQL() resuls
	//which is actually done in the parent... 

        foreach ($sql as $this_sql) {
            ZermeloDatabase::connection($this->getConnectionName())->statement(DB::raw($this_sql));
        }

    }
}
