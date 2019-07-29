<?php

namespace CareSet\Zermelo\Reports\Graph;

use CareSet\Zermelo\Interfaces\CacheInterface;
use CareSet\Zermelo\Models\AbstractGenerator;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloDatabase;
use DB;

class GraphGenerator extends AbstractGenerator
{
    protected $cache = null;
    protected $report = null;

    public function __construct( DatabaseCache $cache )
    {
        $this->cache = $cache;
	$this->report = $cache->getReport();
    }

    /**
     * GraphModelJson
     * Retrieve the nodes and links array to be used with graph
     *
     * @return array
     */
    public function toJson(): array
    {
        /*
        If there is a filter, lets apply it to each column
         */

/*
        $nodes =  ZermeloDatabase::connection($this->cache->getConnectionName())->table($this->cache->getNodeTable())->select("id", "type", "value", "size", "sum_weight","degree")->whereIn('type',$node_types )->get();
        $links = ZermeloDatabase::connection($this->cache->getConnectionName())->table($this->cache->getLinkTable())->select("source", "target", "link_type", "weight")->whereIn('link_type',$link_types)->whereNotNull("source")->whereNotNull("target")->get();
*/


	$cache_table_name_key = $this->cache->getKey();
	$cache_table = $this->cache->getTableName();
	$connection_name = $this->cache->getConnectionName();

	//TODO why are we not using a configuration variable to get the database name everywhere?? Not sure.
	//it seems like we are relying on the connection and that is wrong, because the connection cand change
	//but the right database as configured should not change..

	//should not be hard coded
	$cache_db = "_zermelo_cache";

	$nodes_table = "nodes_$cache_table_name_key";
	$node_types_table = "node_types_$cache_table_name_key";
	$links_table = "links_$cache_table_name_key";
	$link_types_table = "link_types_$cache_table_name_key";

	$sql = [];

	$sql['delete current node table'] = "
DROP TABLE IF EXISTS $cache_db.$nodes_table;
";

	//First we find all of the unique nodes in the from side of the table
	//then union them will all of the unique nodes in the two side of the table..
	//then we create a table of nodes that is the unique nodes shared between the two...
	$sql['create node cache table']  = "
CREATE TABLE $cache_db.$nodes_table AS 
SELECT  
    node_id,
    node_name,
    MAX(node_size) AS node_size,
    node_type,
    node_group,
    node_latitude,
    node_longitude,
    node_img
FROM (

SELECT
    `source_id` AS node_id, 
    `source_name` AS node_name, 
    IF(MAX(`source_size`) > 0, MAX(`source_size`), 50) AS node_size,
    `source_type` AS node_type,
    `source_group` AS node_group, 
    `source_longitude` AS node_longitude, 
    `source_latitude` AS node_latitude, 
    `source_img` AS node_img

FROM $cache_db.$cache_table
GROUP BY `source_id`, `source_name`, `source_type`, `source_group`, `source_longitude`, `source_latitude`, `source_img`

UNION 

SELECT
    `target_id` AS node_id,
    `target_name` AS node_name,
    IF(MAX(`target_size`) > 0, MAX(`target_size`), 50) AS node_size,
    `target_type` AS node_type,
    `target_group` AS node_group,
    `target_longitude` AS node_longitude,
    `target_latitude` AS node_latitude,
    `target_img` AS node_img

FROM $cache_db.$cache_table 
GROUP BY `target_id`, `target_name`, `target_type`, `target_group`, `target_longitude`, `target_latitude`, `target_img` ) AS node_union
GROUP BY node_id, node_name, node_type, node_group, node_longitude, node_latitude, node_img
";

	$sql["lets add an auto indexed primary key to the node table"] = "
ALTER TABLE $cache_db.$nodes_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`); 
";

	//we do this because we need to have something that starts from zero for our JSON indexing..
	$sql["array that starts from zero"] = "
UPDATE $cache_db.$nodes_table SET id = id - 1
";

	$sql["doing joins is better with indexes source side"] = "
ALTER TABLE $cache_db.$cache_table ADD INDEX(`source_id`);
";

	$sql["doing joins is better with indexes target side"] = "
ALTER TABLE $cache_db.$cache_table ADD INDEX(`target_id`);
";

	//Sort the link types table...
	
	$sql["drop link type table"] = "
DROP TABLE IF EXISTS $cache_db.$link_types_table
";
	
	$sql["create link type table"] = "
CREATE TABLE $cache_db.$link_types_table
SELECT DISTINCT
	link_type,
	COUNT(DISTINCT(CONCAT(source_id,target_id))) AS count_distinct_link
FROM $cache_db.$cache_table
GROUP BY link_type
";
	
	$sql["create unique id for link type table"] = "
ALTER TABLE $cache_db.$link_types_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
";

	$sql["the link types table should start from zero"] =  "
UPDATE $cache_db.$link_types_table SET id = id - 1
";


	//Sort the node table...
	
	$sql["drop node type table"] = "
DROP TABLE IF EXISTS $cache_db.$node_types_table
";

	//we use the same "distinct on the results of a union of two distincts" method
	//that we used to sort the nodes... but this time we get a unique list of node types...
	
	$sql["create node type table"] = "
CREATE TABLE $cache_db.$node_types_table
SELECT 	
	node_type, 
	COUNT(DISTINCT(node_id)) AS count_distinct_node
FROM (
		SELECT DISTINCT 
			source_type AS node_type,
			source_id AS node_id
		FROM $cache_db.$cache_table
	UNION 
		SELECT DISTINCT 
			target_type AS node_type,
			target_id AS node_id
		FROM $cache_db.$cache_table 
	) AS  merged_node_type
GROUP BY node_type
";
	
	$sql["create unique id for node type table"] = "
ALTER TABLE $cache_db.$node_types_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
";

	$sql["the node types table should start from zero"] =  "
UPDATE $cache_db.$node_types_table SET id = id - 1
";

	foreach($sql as $this_sql){	
		ZermeloDatabase::connection($connection_name)->statement(DB::raw($this_sql));
	}
	$report_description = $this->report->getReportDescription();
	$report_name = $this->report->getReportName();

	$node_types_sql = "
SELECT 
	node_type AS id,
	node_type AS label,
	CONCAT('/api/',node_type) AS data_url_stub,
	CONCAT(node_type,'_dust') AS dust,
	0 AS is_img,
	'' AS img_stub,
	
";


        return [
		'Report_Name' => $report_name,
		'Report_Description' => $report_description,
            	'node_types' => [],
            	'link_types' => [],
            	'links' => [],
            	'nodes' => [],
            	'cache_meta_generated_this_request' => [],
            	'cache_meta_last_generated' => [],
            	'cache_meta_expire_time' => [],
            	'cache_meta_cache_enabled' => []
        ];
    }
}
