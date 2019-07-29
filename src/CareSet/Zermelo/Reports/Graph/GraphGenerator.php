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
	$node_groups_table = "node_groups_$cache_table_name_key";
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

	$sql["add an index to the string id for the nodes, so tha we can join"] =  "
ALTER TABLE $cache_db.$nodes_table   ADD INDEX(`node_id`);
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


	//Sort the node type table...
	
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

	//we use the same "distinct on the results of a union of two distincts" method
	//that we used to sort the nodes... but this time we get a unique list of node types...
	$sql["drop node group table"] = "
DROP TABLE IF EXISTS $cache_db.$node_groups_table
";
	
	$sql["create node group table"] = "
CREATE TABLE $cache_db.$node_groups_table
SELECT 	
	group_name, 
	COUNT(DISTINCT(node_id)) AS count_distinct_node
FROM (
		SELECT DISTINCT 
			source_group AS group_name,
			source_id AS node_id
		FROM $cache_db.$cache_table
	UNION 
		SELECT DISTINCT 
			target_type AS group_name,
			target_id AS node_id
		FROM $cache_db.$cache_table 
	) AS  merged_node_type
GROUP BY group_name
";
	
	$sql["create unique id for node group table"] = "
ALTER TABLE $cache_db.$node_groups_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
";

	$sql["the node group table should start from zero"] =  "
UPDATE $cache_db.$node_groups_table SET id = id - 1
";







	//loop all over the sql commands and run each one in order...
	foreach($sql as $this_sql){	
		ZermeloDatabase::connection($connection_name)->statement(DB::raw($this_sql));
	}

	$report_description = $this->report->getReportDescription();
	$report_name = $this->report->getReportName();

	//lets read in the node types

	$node_types_sql = "
SELECT 
	id AS my_index,
	node_type AS id,
	node_type AS label,
	CONCAT('/api/',node_type) AS data_url_stub,
	CONCAT(node_type,'_dust') AS dust,
	0 AS is_img,
	'' AS img_stub,
	count_distinct_node AS type_count
FROM $cache_db.$node_types_table	
";
	//lets load the node_types from the database...
	$node_types = [];
	$node_types_result = DB::select(DB::raw($node_types_sql));
	foreach($node_types_result as $this_row){

		//handle the differeces between json and mysql/php here for is_img
		if($this_row->is_img){
			$is_img = false;
		}else{
			$is_img = $this_row->is_img;
		}
		
		$node_types[$this_row->my_index] = [
			'id' => $this_row->id,
			'label' => $this_row->label,
			'data_url_stub' => $this_row->data_url_stub,
			'dust' => $this_row->dust,
			'is_img' => $is_img,
			'img_stub' => $this_row->img_stub,
			'type_count' => $this_row->type_count,
			];
	}

	//lets read in the link types

	$link_types_sql = "
SELECT 
	id AS my_index,
	link_type AS label,
	count_distinct_link AS link_type_count
FROM $cache_db.$link_types_table	
";
	//lets load the link_types from the database...
	$link_types = [];
	$link_types_result = DB::select(DB::raw($link_types_sql));
	foreach($link_types_result as $this_row){

		$link_types[$this_row->my_index] = [
			'id' => $this_row->label,
			'label' => $this_row->label,
			'link_type_count' => $this_row->link_type_count,
			];
	}

	//lets read in the link types

	$group_sql = "
SELECT 
	id AS my_index,
	group_name AS id,
	group_name AS name,
	count_distinct_node AS group_count
FROM $cache_db.$node_groups_table	
";

	//lets load the link_types from the database...
	$node_groups = [];
	$node_groups_result = DB::select(DB::raw($group_sql));
	foreach($node_groups_result as $this_row){

		$node_groups[$this_row->my_index] = [
			'id' => $this_row->id,
			'name' => $this_row->name,
			'group_count' => $this_row->group_count,
			];
	}

	//lets sort the nodes
	$nodes_sql = "
SELECT 
	`node_name` AS name,
	`node_latitude` AS latitude,
	`node_longitude` AS longitude,
	groups.id AS `group`,
	node_size AS size,
	node_img AS img,
	nodes.id AS `type`,
	`node_id` AS id,
	0 AS weight_sum,
	0 AS degree,
	nodes.id AS my_index
FROM $cache_db.$nodes_table AS nodes
LEFT JOIN $cache_db.$node_groups_table AS groups ON 
	groups.group_name =
    	node_group
LEFT JOIN $cache_db.$node_types_table AS types ON 
	types.node_type = 
    	nodes.node_type 
ORDER BY nodes.id DESC
";
	//lets load the link_types from the database...
	$nodes = [];
	$nodes_result = DB::select(DB::raw($nodes_sql));
	foreach($nodes_result as $this_row){

		if(is_null($this_row->img)){
			$img = false;
		}else{
			$img = $this_row->img;
		}

		//we would this version result in an object instead of an array?? confusing
//		$nodes[$this_row->my_index] = [
		$nodes[] = [
				'name' => $this_row->name,
				'short_name' => substr($this_row->name,0,20),
				'longitude' => $this_row->longitude,
				'latitiude' => $this_row->latitude,
				'group' => (int) $this_row->group,
				'size' => (int) $this_row->size,
				'img' => $img,
				'type' => (int) $this_row->type,
				'id' => $this_row->id,
				'weight_sum' => (int) $this_row->weight_sum,
				'degree' => (int) $this_row->degree,
				'my_index' => (int) $this_row->my_index,
			];
	}



	//now we put it all together to return the results...
        return [
		'Report_Name' => $report_name,
		'Report_Description' => $report_description,
		'groups' => $node_groups,
            	'types' => $node_types,
            	'link_types' => $link_types,
            	'links' => [],
            	'nodes' => $nodes,
            	'cache_meta_generated_this_request' => [],
            	'cache_meta_last_generated' => [],
            	'cache_meta_expire_time' => [],
            	'cache_meta_cache_enabled' => []
        ];
    }



}
