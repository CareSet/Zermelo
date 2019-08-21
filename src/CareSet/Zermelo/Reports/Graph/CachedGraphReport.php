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
    protected $nodes_table = null;
    protected $node_types_table = null;
    protected $node_groups_table = null;
    protected $link_table = null;
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
        // If we are rebuilding the cache in this request, we generate the auxillary graph cache tables below
        parent::__construct($report, $connectionName);

        $cache_table_name_key = $this->getKey();
        $this->nodes_table = "nodes_$cache_table_name_key";
        $this->node_types_table = "node_types_$cache_table_name_key";
        $this->node_groups_table = "node_groups_$cache_table_name_key";
        $this->links_table = "links_$cache_table_name_key";
        $this->link_types_table = "link_types_$cache_table_name_key";
        $this->summary_table = "summary_$cache_table_name_key";

        //$this->node_table = $this->keygen('GraphNode');
       // $this->link_table = $this->keygen('GraphLinks');

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
        /*
        $NodeColumns = $this->getReport()->getNodeDefinitions();
        $LinkColumns = $this->getReport()->getLinkDefinitions();

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
        */

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
    public function getLinkTable()
    {
        return $this->link_table;
    }

    /**
     * @param null|string $link_table
     */
    public function setLinkTable($link_table)
    {
        $this->link_table = $link_table;
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

    private function createGraphTables()
    {
        $start_time = microtime(true);
        $cache_table = $this->getTableName();
        $connection_name = $this->getConnectionName();
        $cache_db = zermelo_cache_db();
        $sql = [];

        $sql['delete current node table'] = "
DROP TABLE IF EXISTS $cache_db.$this->nodes_table;
";

        // Build the query that builds the nodes table. This will use all of the node definitions in our report, and
        // union them together
        $node_sql_parts = [];
        foreach ($this->getReport()->getNodeDefinitions() as $nodeDefinition) {
            if ($nodeDefinition instanceof NodeDefinitionIF) {
                $node_sql = new \stdClass();
                $node_sql->SELECT = "{$nodeDefinition->getNodeId()} AS `node_id`,
                    `{$nodeDefinition->getNodeName()}` AS `node_name`,
                    {$nodeDefinition->getNodeSizeFormula()} AS `node_size`,
                    `{$nodeDefinition->getNodeType()}` AS `node_type`,
                    `{$nodeDefinition->getNodeGroup()}` AS `node_group`,
                    `{$nodeDefinition->getNodeLatitude()}` AS `node_latitude`,
                    `{$nodeDefinition->getNodeLongitude()}` AS `node_longitude`,
                    `{$nodeDefinition->getNodeImg()}` AS `node_img`";

                $node_sql->GROUP_BY = "`{$nodeDefinition->getNodeId()}`,
                    `{$nodeDefinition->getNodeName()}`,
                    `{$nodeDefinition->getNodeSize()}`,
                    `{$nodeDefinition->getNodeType()}`,
                    `{$nodeDefinition->getNodeGroup()}`,
                    `{$nodeDefinition->getNodeLatitude()}`,
                    `{$nodeDefinition->getNodeLongitude()}`,
                    `{$nodeDefinition->getNodeImg()}`";
                $node_sql_parts[]= $node_sql;
            }
        }


        //First we find all of the unique nodes in the from side of the table
        //then union them will all of the unique nodes in the two side of the table..
        //then we create a table of nodes that is the unique nodes shared between the two...
        $sql['create node cache table'] = "CREATE TABLE $cache_db.$this->nodes_table AS 
        SELECT  
            node_id,
            node_name,
            MAX(node_size) AS node_size,
            node_type,
            node_group,
            node_latitude,
            node_longitude,
            node_img
        FROM (";

        $count = 0;
        foreach ($node_sql_parts as $node_sql_part) {
            $sql['create node cache table'] .= "SELECT $node_sql_part->SELECT FROM $cache_db.$cache_table GROUP BY $node_sql_part->GROUP_BY";
            if ($count < count($node_sql_parts) - 1) {
                $sql['create node cache table'] .= "\nUNION\n";
            }
            $count++;
        }

        $sql['create node cache table'] .= " ) AS node_union
        GROUP BY node_id, node_name, node_type, node_group, node_longitude, node_latitude, node_img";

        $sql["lets add an auto indexed primary key to the node table"] = "
ALTER TABLE $cache_db.$this->nodes_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`); 
";

        $sql["add an index to the string id for the nodes, so tha we can join"] = "
ALTER TABLE $cache_db.$this->nodes_table   ADD INDEX(`node_id`);
";


        //we do this because we need to have something that starts from zero for our JSON indexing..
        $sql["array that starts from zero"] = "
UPDATE $cache_db.$this->nodes_table SET id = id - 1
";

        $sql["doing joins is better with indexes source side"] = "
ALTER TABLE $cache_db.$cache_table ADD INDEX(`source_id`);
";

        $sql["doing joins is better with indexes target side"] = "
ALTER TABLE $cache_db.$cache_table ADD INDEX(`target_id`);
";

        //Sort the link types table...

        $sql["drop link type table"] = "
DROP TABLE IF EXISTS $cache_db.$this->link_types_table
";

        $sql["create link type table"] = "
CREATE TABLE $cache_db.$this->link_types_table
SELECT DISTINCT
	link_type,
	COUNT(DISTINCT(CONCAT(source_id,target_id))) AS count_distinct_link
FROM $cache_db.$cache_table
GROUP BY link_type
";

        $sql["create unique id for link type table"] = "
ALTER TABLE $cache_db.$this->link_types_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
";

        $sql["the link types table should start from zero"] = "
UPDATE $cache_db.$this->link_types_table SET id = id - 1
";


        //Sort the node type table...

        $sql["drop node type table"] = "
DROP TABLE IF EXISTS $cache_db.$this->node_types_table
";

        //we use the same "distinct on the results of a union of two distincts" method
        //that we used to sort the nodes... but this time we get a unique list of node types...

        $sql["create node type table"] = "
CREATE TABLE $cache_db.$this->node_types_table
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
ALTER TABLE $cache_db.$this->node_types_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
";

        $sql["the node types table should start from zero"] = "
UPDATE $cache_db.$this->node_types_table SET id = id - 1
";

        //we use the same "distinct on the results of a union of two distincts" method
        //that we used to sort the nodes... but this time we get a unique list of node types...
        $sql["drop node group table"] = "
DROP TABLE IF EXISTS $cache_db.$this->node_groups_table
";

        $sql["create node group table"] = "
CREATE TABLE $cache_db.$this->node_groups_table
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
ALTER TABLE $cache_db.$this->node_groups_table ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
";

        $sql["the node group table should start from zero"] = "
UPDATE $cache_db.$this->node_groups_table SET id = id - 1
";


        $sql["drop the summary table"] = "
DROP TABLE IF EXISTS $cache_db.$this->summary_table;
";

        $sql["create the summary table with the group count"] = "
CREATE TABLE $cache_db.$this->summary_table AS 
SELECT 
	'group_count                            ' AS summary_key,
	COUNT(DISTINCT(group_name))  AS summary_value
FROM $cache_db.$this->node_groups_table
";

        $sql["add the type count"] = "
INSERT INTO $cache_db.$this->summary_table
SELECT 
	'type_count' AS summary_key,
	COUNT(DISTINCT(node_type)) AS summary_value
FROM $cache_db.$this->node_types_table
";

        $sql["add the node count"] = "
INSERT INTO $cache_db.$this->summary_table
SELECT 
	'nodes_count' AS summary_key,
	COUNT(DISTINCT(`id`)) AS summary_value
FROM $cache_db.$this->nodes_table
";

        $sql["add the edge count"] = "
INSERT INTO $cache_db.$this->summary_table
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