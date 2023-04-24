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

class CachedGraphReport extends DatabaseCache
{
    protected $cache_db = '_zermelo_cache';

    protected $nodes_table = null;
    protected $node_types_table = null;
    protected $node_groups_table = null;
    protected $links_table = null;
    protected $link_types_table = null;
    protected $summary_table = null;

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
    public function __construct(AbstractGraphReport $report, $connectionName)
    {

        // create cache tables, the logic in handled in the superclass constructor, and it only generates new table if required
        // If we are rebuilding the cache in this request, the parent will generate a table with the results from the report's
        // GetSQL() function query. Then, we generate the auxillary graph cache tables below
        parent::__construct($report, $connectionName);

        // Get our cache key from parent, and use it to name all of our auxiliary graph tables
        $cache_table_name_key = $this->getKey();
        $this->nodes_table = "nodes_$cache_table_name_key";
        $this->node_types_table = "node_types_$cache_table_name_key";
        $this->node_groups_table = "node_groups_$cache_table_name_key";
        $this->links_table = "links_$cache_table_name_key";
        $this->link_types_table = "link_types_$cache_table_name_key";
        $this->summary_table = "summary_$cache_table_name_key";

	//TODO this should come from configuration...
	$this->cache_db = '_zermelo_cache'; 

        // Only generate the aux tables (drop and re-create) if dictated by cache rules
        if ($this->getGeneratedThisRequest() === true) {
            $this->createGraphTables();
        }
    }

    /**
     * @return null|string
     */
    public function getNodesTable()
    {
        return $this->nodes_table;
    }

    /**
     * @param null|string $nodes_table
     */
    public function setNodesTable($nodes_table)
    {
        $this->nodes_table = $nodes_table;
    }

    /**
     * @return null
     */
    public function getNodeTypesTable()
    {
        return $this->node_types_table;
    }

    /**
     * @param null $node_types_table
     */
    public function setNodeTypesTable($node_types_table)
    {
        $this->node_types_table = $node_types_table;
    }

    /**
     * @return null
     */
    public function getNodeGroupsTable()
    {
        return $this->node_groups_table;
    }

    /**
     * @param null $node_groups_table
     */
    public function setNodeGroupsTable($node_groups_table)
    {
        $this->node_groups_table = $node_groups_table;
    }

    /**
     * @return null|string
     */
    public function getLinksTable()
    {
        return $this->links_table;
    }

    /**
     * @param null|string $links_table
     */
    public function setLinksTable($links_table)
    {
        $this->links_table = $links_table;
    }

    /**
     * @return null
     */
    public function getLinkTypesTable()
    {
        return $this->link_types_table;
    }

    /**
     * @param null $link_types_table
     */
    public function setLinkTypesTable($link_types_table)
    {
        $this->link_types_table = $link_types_table;
    }

    /**
     * @return null
     */
    public function getSummaryTable()
    {
        return $this->summary_table;
    }

    /**
     * @param null $summary_table
     */
    public function setSummaryTable($summary_table)
    {
        $this->summary_table = $summary_table;
    }


    public function getVisibleNodeTypes()
    {
        return $this->visible_node_types;
    }

    public function getVisibleLinkTypes()
    {
        return $this->visible_link_types;
    }

    // Get the node types and link types from user input
    // TODO this is not used in this implementation, needs to be considered
    public function typesLookup()
    {
        // Perhaps this stuff should go in the JSON generation since it doesn't realate to cache, but display only
        $input_node_types = [];
        if ($this->getReport()->getInput('node_types') && is_array($this->getReport()->getInput('node_types'))) {
            $input_node_types = $this->getReport()->getInput('node_types');
        }

        $input_link_types = [];
        if ($this->getReport()->getInput('link_types') && is_array($this->getReport()->getInput('link_types'))) {
            $input_link_types = $this->getReport()->getInput('link_types');
        }

        // Go ahead to build the lookup arrays that represent the node types and link types of the graph based on
        // the node and link definitions in our report
        $fields = ZermeloDatabase::getTableColumnDefinition($this->getTableName(), $this->connectionName);
        $node_index = 0;
        $link_index = 0;

        // These are the columns of the table to treat as Nodes and Links

        // Look at each field in our GetSQL() result table, and get all of our node types and link types, and
        // TODO Do some validation to make sure all of our nodes and links columns are actually in the table
        foreach ($fields as $field) {
            $column = $field['Name'];
            $title = ucwords(str_replace('_', ' ', $column), "\t\r\n\f\v ");
            if (ZermeloDatabase::isColumnInKeyArray($column, $this->getReport()->getNodeTypeColumns())) {
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
            if (ZermeloDatabase::isColumnInKeyArray($column, $this->getReport()->getLinkTypeColumns())) {
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

    /**
     * This is where the graph tables are calculated.
     *
     * groups is the groups is the "group" lookup array,
     * The 'types' table is the type lookup array.
     * The 'nodes' table is the graph's nodes array, which has references to the groups and to the types.
     * The 'links' table has references to the node table and to the link_types array lookup table.
     * The config array is empty for now, cannot remember what I put there.
     * the 'summary' key has data about the graph...
     */
    private function createGraphTables()
    {
        $start_time = microtime(true);
        $sql = [];

        $sql['delete current node table'] = "DROP TABLE IF EXISTS $this->cache_db.$this->nodes_table;";

        // Build the query that builds the nodes table. This will take the source and target nodes and union them together
        // First we find all of the unique nodes in the from side of the table
        // then union them will all of the unique nodes in the two side of the table..
        // then we create a table of nodes that is the unique nodes shared between the two...

	$sql['create the node cache table'] = "
CREATE TABLE $this->cache_db.$this->nodes_table (
  `id` int(11) NOT NULL,
  `node_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `node_name` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  `node_size` bigint(20) DEFAULT NULL,
  `node_type` varchar(1000) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `node_group` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `node_latitude` decimal(17,7) NOT NULL DEFAULT 0,
  `node_longitude` decimal(17,7) NOT NULL DEFAULT 0,
  `node_json_url` varchar(2000) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `node_img` varchar(1000) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
";
	
	//now we make rules to ensure that we have a SQL crash here if the node uniqueness rules are not followed.

	$sql['enforce uniqueness on the node_id of the table..'] = "
ALTER TABLE $this->cache_db.$this->nodes_table
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `node_id` (`node_id`);
";

	$sql['and auto increment the id'] = "
ALTER TABLE $this->cache_db.$this->nodes_table
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
";

        $sql['populate node cache table'] =
            "INSERT INTO $this->cache_db.$this->nodes_table 
            SELECT  
		NULL AS id,
                node_id,
                node_name,
                MAX(node_size) AS node_size,
                node_type,
                node_group,
                node_latitude,
                node_longitude,
		node_json_url,
                node_img
            FROM 
            (
                SELECT
                    `source_id` AS node_id, 
                    `source_name` AS node_name, 
                    IF(MAX(`source_size`) > 0, MAX(`source_size`), 50) AS node_size,
                    `source_type` AS node_type,
                    `source_group` AS node_group, 
                    `source_longitude` AS node_longitude, 
                    `source_latitude` AS node_latitude, 
		    `source_json_url` AS node_json_url,
                    `source_img` AS node_img
                
                FROM $this->cache_db.`{$this->getTableName()}`
                GROUP BY `source_id`, `source_name`, `source_type`, `source_group`, `source_longitude`, `source_latitude`, `source_json_url`, `source_img`
                
                UNION 
                
                SELECT
                    `target_id` AS node_id,
                    `target_name` AS node_name,
                    IF(MAX(`target_size`) > 0, MAX(`target_size`), 50) AS node_size,
                    `target_type` AS node_type,
                    `target_group` AS node_group,
                    `target_longitude` AS node_longitude,
                    `target_latitude` AS node_latitude,
		    `target_json_url` AS node_json_url,
                    `target_img` AS node_img
                
                FROM $this->cache_db.`{$this->getTableName()}`
                GROUP BY `target_id`, `target_name`, `target_type`, `target_group`, `target_longitude`, `target_latitude`, `target_json_url`, `target_img` 
            ) 
            AS node_union
            GROUP BY node_id, node_name, node_type, node_group, node_latitude, node_longitude, node_json_url, node_img
";

        //we do this because we need to have something that starts from zero for our JSON indexing..
        $sql["array that starts from zero"] =
            "UPDATE $this->cache_db.$this->nodes_table SET id = id - 1";

        // For all the IDs defined in the node definitions, add an index for them
        $sql["doing joins is better with indexes source side"] =
            "ALTER TABLE $this->cache_db.`{$this->getTableName()}` ADD INDEX(`source_id`);";

        $sql["doing joins is better with indexes, add to target side"] =
            "ALTER TABLE $this->cache_db.`{$this->getTableName()}` ADD INDEX(`target_id`);";

        // At this point, we've set up creation of the nodes table. We're done with nodes!
        // Now we work on Links

        // Create the link types lookup table
        $sql["drop link type table"] =
            "DROP TABLE IF EXISTS $this->cache_db.$this->link_types_table";

        // Gather all the IDs from the node definitions so we can concat them and count them
        // We wind up with a table containing all unique link types and a count of how many
        // node pairs there are of this link type
        $sql["create link type table"] =
            "CREATE TABLE $this->cache_db.$this->link_types_table
            SELECT DISTINCT
                link_type,
                COUNT(DISTINCT(CONCAT(`source_id`,`target_id`))) AS count_distinct_link
            FROM $this->cache_db.`{$this->getTableName()}`
            GROUP BY link_type
            ";

        $sql["create unique id for link type table"] =
            "ALTER TABLE $this->cache_db.$this->link_types_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);";

        $sql["the link types table should start from zero"] = "UPDATE $this->cache_db.$this->link_types_table SET id = id - 1;";

        // Now create the links table
        // First drop the links table if it already exists
        $sql["drop links table"] = "DROP TABLE IF EXISTS $this->cache_db.$this->links_table;";

	
	$sql["create the links table with indexes"] = "
CREATE TABLE $this->cache_db.`$this->links_table` (
  `source` int(11) NOT NULL DEFAULT 0,
  `target` int(11) NOT NULL DEFAULT 0,
  `weight` decimal(15,5) NOT NULL,
  `link_type` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";

	$sql["primary key to prevent duplicates later on"] ="
ALTER TABLE $this->cache_db.`$this->links_table`
  ADD PRIMARY KEY (`source`,`target`,`link_type`);
";


        // Build the links table
        $sql["create links table"] =
            "INSERT IGNORE $this->cache_db.`$this->links_table` 
            SELECT 
                source_nodes.id AS `source`,
                target_nodes.id AS `target`, 
                `weight`, 
                link_types.id AS `link_type`
            FROM $this->cache_db.{$this->getTableName()} AS graph
            JOIN $this->cache_db.{$this->nodes_table} AS source_nodes 
            ON source_nodes.node_id = graph.source_id
            JOIN $this->cache_db.{$this->nodes_table} AS target_nodes 
            ON target_nodes.node_id = graph.target_id  
            JOIN $this->cache_db.{$this->link_types_table} AS link_types 
            ON link_types.link_type = graph.link_type
        ";


        //Sort the node type table...

        $sql["drop node type table"] = "DROP TABLE IF EXISTS $this->cache_db.$this->node_types_table";

        //we use the same "distinct on the results of a union of two distincts" method
        //that we used to sort the nodes... but this time we get a unique list of node types...

        $sql["create node type table"] =
            "CREATE TABLE $this->cache_db.$this->node_types_table
            SELECT 	
                node_type, 
                COUNT(DISTINCT(node_id)) AS count_distinct_node
            FROM (
                    SELECT DISTINCT 
                        source_type AS node_type,
                        source_id AS node_id
                    FROM $this->cache_db.`{$this->getTableName()}`
                UNION 
                    SELECT DISTINCT 
                        target_type AS node_type,
                        target_id AS node_id
                    FROM $this->cache_db.`{$this->getTableName()}`
                ) AS  merged_node_type
            GROUP BY node_type
	";

        $sql["create unique id for node type table"] =
            "ALTER TABLE $this->cache_db.`{$this->node_types_table}` ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);";

        $sql["the node types table should start from zero"] = "UPDATE $this->cache_db.{$this->node_types_table} SET id = id - 1";

        //we use the same "distinct on the results of a union of two distincts" method
        //that we used to sort the nodes... but this time we get a unique list of node types...
        $sql["drop node group table"] = "DROP TABLE IF EXISTS $this->cache_db.{$this->node_groups_table}";

        $sql["create node group table"] =
            "CREATE TABLE $this->cache_db.{$this->node_groups_table}
            SELECT 	
                group_name, 
                COUNT(DISTINCT(node_id)) AS count_distinct_node
            FROM (
                    SELECT DISTINCT 
                        source_group AS group_name,
                        source_id AS node_id
                    FROM $this->cache_db.`{$this->getTableName()}`
                UNION 
                    SELECT DISTINCT 
                        target_type AS group_name,
                        target_id AS node_id
                    FROM $this->cache_db.`{$this->getTableName()}`
                ) AS  merged_node_type
            GROUP BY group_name";

        $sql["create unique id for node group table"] =
            "ALTER TABLE $this->cache_db.$this->node_groups_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);";

        $sql["the node group table should start from zero"] =
            "UPDATE $this->cache_db.$this->node_groups_table SET id = id - 1;";

        $sql["drop the summary table"] = "DROP TABLE IF EXISTS $this->cache_db.$this->summary_table;";

	$sql["create the summary table with varchar to be sage"] =  "
CREATE TABLE $this->cache_db.$this->summary_table (
  `summary_key` varchar(39) NOT NULL,
  `summary_value` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";

        $sql["create the summary table with the group count"] =
            "INSERT INTO $this->cache_db.$this->summary_table 
            SELECT 
                'group_count                            ' AS summary_key,
                COUNT(DISTINCT(group_name))  AS summary_value
            FROM $this->cache_db.$this->node_groups_table";

        $sql["add the type count"] =
            "INSERT INTO $this->cache_db.$this->summary_table
            SELECT 
                'type_count' AS summary_key,
                COUNT(DISTINCT(node_type)) AS summary_value
            FROM $this->cache_db.$this->node_types_table";

        $sql["add the node count"] =
            "INSERT INTO $this->cache_db.$this->summary_table
            SELECT 
                'nodes_count' AS summary_key,
                COUNT(DISTINCT(`id`)) AS summary_value
            FROM $this->cache_db.$this->nodes_table";

        $sql["add the edge count"] =
            "INSERT INTO $this->cache_db.$this->summary_table
            SELECT 
                'links_count' AS summary_key,
                COUNT(DISTINCT(CONCAT(source_id,target_id))) AS summary_value
            FROM $this->cache_db.`{$this->getTableName()}`";

	$pdo = ZermeloDatabase::connection($this->getConnectionName())->getPdo();

        //loop all over the sql commands and run each one in order...
        // The connection is a DB Connection to our CACHE DATABASE using the credentials
        // The connection is created in CareSet\Zermelo\Models\ZermeloDatabsse
        foreach ($sql as $this_sql) {
            $pdo->exec($this_sql);
        }


        $time_elapsed = microtime(true) - $start_time;

	//TODO this seems to fail regularly. Forcing the value to 1 to stabilize needs to be debugged
	$time_elapsed = 1;
	
        $processing_time_sql = "INSERT INTO $this->cache_db.$this->summary_table
            SET 
		summary_key = 'seconds_to_process',
            	summary_value = '$time_elapsed'
;
";
        $pdo->exec($processing_time_sql);
    }
}
