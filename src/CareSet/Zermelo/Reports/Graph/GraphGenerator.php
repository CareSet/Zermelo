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

    public function __construct(CachedGraphReport $cache)
    {
        $this->cache = $cache;
        $this->report = $cache->getReport();
    }

    /**
     * GraphModelJson
     * Retrieve the nodes and links array to be used with graph from the appropriate cached table
     *
     * @return array
     */
    public function toJson(): array
    {

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
FROM {$this->cache->getNodeTypesTable()}	
";
        //lets load the node_types from the database...
        $node_types = [];
        $node_types_result = ZermeloDatabase::connection($this->cache->getConnectionName())->select(DB::raw($node_types_sql));
        foreach ($node_types_result as $this_row) {

            //handle the differeces between json and mysql/php here for is_img
            if ($this_row->is_img) {
                $is_img = false;
            } else {
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
FROM {$this->cache->getLinkTypesTable()}	
";
        //lets load the link_types from the database...
        $link_types = [];
        $link_types_result = ZermeloDatabase::connection($this->cache->getConnectionName())->select(DB::raw($link_types_sql));
        foreach ($link_types_result as $this_row) {

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
FROM {$this->cache->getNodeGroupsTable()}	
";

        //lets load the link_types from the database...
        $node_groups = [];
        $node_groups_result = ZermeloDatabase::connection($this->cache->getConnectionName())->select(DB::raw($group_sql));
        foreach ($node_groups_result as $this_row) {

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
FROM {$this->cache->getNodesTable()} AS nodes
LEFT JOIN {$this->cache->getNodeGroupsTable()} AS groups ON 
	groups.group_name =
    	node_group
LEFT JOIN {$this->cache->getNodeTypesTable()} AS types ON 
	types.node_type = 
    	nodes.node_type 
ORDER BY nodes.id DESC
";
        //lets load the link_types from the database...
        $nodes = [];
        $nodes_result = ZermeloDatabase::connection($this->cache->getConnectionName())->select(DB::raw($nodes_sql));
        foreach ($nodes_result as $this_row) {

            if (is_null($this_row->img)) {
                $img = false;
            } else {
                $img = $this_row->img;
            }

            //we would this version result in an object instead of an array?? confusing
//		$nodes[$this_row->my_index] = [
            $nodes[] = [
                'name' => $this_row->name,
                'short_name' => substr($this_row->name, 0, 20),
                'longitude' => $this_row->longitude,
                'latitiude' => $this_row->latitude,
                'group' => (int)$this_row->group,
                'size' => (int)$this_row->size,
                'img' => $img,
                'type' => (int)$this_row->type,
                'id' => $this_row->id,
                'weight_sum' => (int)$this_row->weight_sum,
                'degree' => (int)$this_row->degree,
                'my_index' => (int)$this_row->my_index,
            ];
        }

        // Retrieve the links from the DB
        $links_sql = "SELECT * FROM `{$this->cache->getLinksTable()}`";
        $links = [];
        $links_result = ZermeloDatabase::connection($this->cache->getConnectionName())->select(DB::raw($links_sql));
        foreach ($links_result as $this_row) {

            $links[] = [
                'source' => $this_row->source,
                'target' => $this_row->target,
                'weight' => $this_row->weight,
                'link_type' => $this_row->link_type,
            ];
        }

        //lets export the summary data on the graph
        $summary_sql = "
            SELECT 
                summary_key,
                summary_value
            FROM {$this->cache->getSummaryTable()}";

        $summary = [];
        $summary_result = ZermeloDatabase::connection($this->cache->getConnectionName())->select(DB::raw($summary_sql));
        foreach ($summary_result as $this_row) {
            $summary[][$this_row->summary_key] = $this_row->summary_value;
        }

        //$time_elapsed = microtime(true) - $start_time;

        $summary[]['seconds_to_process'] = '9898989898';

        //now we put it all together to return the results...
        return [
            'careset_name' => 'For backwards compatibility',
            'careset_code' => '1112223334',
            'Report_Name' => $report_name,
            'Report_Description' => $report_description,
            'Report_Key' => $this->cache->getKey(),
            'summary' => $summary,
            'config' => [], //not implemented..
            'groups' => $node_groups,
            'types' => $node_types,
            'link_types' => $link_types,
            'nodes' => $nodes,
            'links' => $links,
        ];
    }


}
