<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/24/18
 * Time: 2:47 PM
 */

namespace CareSet\Zermelo\Reports\Graph;


use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloReport;
use CareSet\Zermelo\Models\ZermeloDatabase;
use \DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CachedGraphReport extends DatabaseCache
{
    protected $graph_nodes_table = null;
    protected $graph_weights_table = null;

    protected $node_table = null;
    protected $link_table = null;

    protected $node_types = [];
    protected $link_types = [];

    protected $visible_node_types = [];
    protected $visible_link_types = [];

    /**
     * CachedGraphReport constructor.
     *
     * @param ZermeloReport $report The report to be cached
     *
     * @param $connectionName The name of the Cache Database connection, which represents the cache database name, and credentials for connecting
     */
    public function __construct(ZermeloReport $report, $connectionName)
    {
        // create cache tables, the logic in handled in the superclass constructor, and it only generates new table if required
        // We override the createTable() function in this subclass to also generate the graph cache tables
        parent::__construct($report, $connectionName);

        $this->node_table = $this->keygen('GraphNode');
        $this->link_table = $this->keygen('GraphLinks');

        // Get the node types and link types from user input
        // TODO this is not used in this implementation, needs to be considered
        $input_node_types = [];
        if ($this->getReport()->getInput('node_types') && is_array($this->getReport()->getInput('node_types'))) {
            $input_node_types = $this->getReport()->getInput('node_types');
        }

        $input_link_types = [];
        if ($this->getReport()->getInput('link_types') && is_array($this->getReport()->getInput('link_types'))) {
            $input_link_types = $this->getReport()->getInput('link_types');
        }

        // Go ahead to build the auxilliary tables that represent the nodes and links of the graph
        $fields = ZermeloDatabase::getTableColumnDefinition($this->getTableName(), $this->connectionName);
        $node_index = 0;
        $link_index = 0;

        // These are the columns of the table to treat as Nodes and Links
        $NodeColumns = $this->getReport()->NODES;
        $LinkColumns = $this->getReport()->LINKS;

        foreach ($fields as $field) {
            $column = $field['Name'];
            $title = ucwords(str_replace('_', ' ', $column), "\t\r\n\f\v ");
            if (ZermeloDatabase::isColumnInKeyArray($column, $NodeColumns)) {
                $subjects_found[] = $column;
                $this->node_types[$node_index] = [
                    'id' => $node_index,
                    'field' => $column,
                    'name' => $title,
                    'visible' => in_array($node_index, $input_node_types)
                ];
                $this->visible_node_types[$node_index] = $this->node_types[$node_index]['visible'];
                ++$node_index;
            }
            if (ZermeloDatabase::isColumnInKeyArray($column, $LinkColumns)) {
                $weights_found[] = $column;
                $this->link_types[$link_index] = [
                    'id' => $link_index,
                    'field' => $column,
                    'name' => $title,
                    'visible' => in_array($link_index, $input_link_types)
                ];
                $this->visible_link_types[$link_index] = $this->link_types[$link_index]['visible'];
                ++$link_index;
            }
        }

        if (!is_array($this->node_types) || empty($this->node_types)) {
            for ($i = 2, $len = count($this->node_types); $i < $len; ++$i) {
                $this->node_types[$i]['visible'] = false;
                $this->visible_node_types[$i] = false;
            }
        }
    }

    public function getNodeTable()
    {
        return $this->node_table;
    }

    public function getLinkTable()
    {
        return $this->link_table;
    }

    public function getNodeTypes()
    {
        return $this->node_types;
    }

    public function getLinkTypes()
    {
        return $this->link_types;
    }

    public function getVisibleNodeTypes()
    {
        return $this->visible_node_types;
    }

    public function getVisibleLinkTypes()
    {
        return $this->visible_link_types;
    }

    /**
     * This function is called by the parent class when the cache has to be regenerated,
     * so create the "main" table, and then the aux node/link tables
     */
    public function createTable()
    {
        parent::createTable();

        $this->createGraphTables();
    }

    private function createGraphTables()
    {
        $start_time = microtime(true);

        $cache_table_name_key = $this->getKey();
        $cache_table = $this->getTableName();
        $connection_name = $this->getConnectionName();

        $cache_db = zermelo_cache_db();

        $nodes_table = "nodes_$cache_table_name_key";
        $node_types_table = "node_types_$cache_table_name_key";
        $node_groups_table = "node_groups_$cache_table_name_key";
        $links_table = "links_$cache_table_name_key";
        $link_types_table = "link_types_$cache_table_name_key";
        $summary_table = "summary_$cache_table_name_key";

        $sql = [];

        $sql['delete current node table'] = "
DROP TABLE IF EXISTS $cache_db.$nodes_table;
";

        //First we find all of the unique nodes in the from side of the table
        //then union them will all of the unique nodes in the two side of the table..
        //then we create a table of nodes that is the unique nodes shared between the two...
        $sql['create node cache table'] = "
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

        $sql["add an index to the string id for the nodes, so tha we can join"] = "
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

        $sql["the link types table should start from zero"] = "
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

        $sql["the node types table should start from zero"] = "
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

        $sql["the node group table should start from zero"] = "
UPDATE $cache_db.$node_groups_table SET id = id - 1
";


        $sql["drop the summary table"] = "
DROP TABLE IF EXISTS $cache_db.$summary_table;
";

        $sql["create the summary table with the group count"] = "
CREATE TABLE $cache_db.$summary_table AS 
SELECT 
	'group_count                            ' AS summary_key,
	COUNT(DISTINCT(group_name))  AS summary_value
FROM $cache_db.$node_groups_table
";

        $sql["add the type count"] = "
INSERT INTO $cache_db.$summary_table
SELECT 
	'type_count' AS summary_key,
	COUNT(DISTINCT(node_type)) AS summary_value
FROM $cache_db.$node_types_table
";

        $sql["add the node count"] = "
INSERT INTO $cache_db.$summary_table
SELECT 
	'nodes_count' AS summary_key,
	COUNT(DISTINCT(`id`)) AS summary_value
FROM $cache_db.$nodes_table
";

        $sql["add the edge count"] = "
INSERT INTO $cache_db.$summary_table
SELECT 
	'links_count' AS summary_key,
	COUNT(DISTINCT(CONCAT(source_id,target_id))) AS summary_value
FROM $cache_db.$cache_table
";

        //loop all over the sql commands and run each one in order...
        foreach ($sql as $this_sql) {
            ZermeloDatabase::connection($connection_name)->statement(DB::raw($this_sql));
        }

    }

}