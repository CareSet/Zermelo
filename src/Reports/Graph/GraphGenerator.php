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
    protected $cache_db = '_zermelo_cache'; //TODO this should be set in config

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
	$cache_db = '_zermelo_cache'; //should be coming from config TODO

        //lets read in the node types

        $node_types_sql = "
SELECT 
	CAST(CONVERT(id USING utf8) AS binary) AS my_index,
	CAST(CONVERT(node_type USING utf8) AS binary) AS id,
	CAST(CONVERT(node_type USING utf8) AS binary) AS label,
	0 AS is_img,
	'' AS img_stub,
	CAST(CONVERT(count_distinct_node USING utf8) AS binary) AS type_count
FROM $this->cache_db.{$this->cache->getNodeTypesTable()}	
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
                'is_img' => $is_img,
                'img_stub' => $this_row->img_stub,
                'type_count' => $this_row->type_count,
            ];
        }

        //lets read in the link types

        $link_types_sql = "
SELECT 
	CAST(CONVERT(id USING utf8) AS binary) AS my_index,
	CAST(CONVERT(link_type USING utf8) AS binary) AS label,
	CAST(CONVERT(count_distinct_link USING utf8) AS binary) AS link_type_count
FROM $this->cache_db.{$this->cache->getLinkTypesTable()}	
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
	CAST(CONVERT(id USING utf8) AS binary) AS my_index,
	CAST(CONVERT(group_name USING utf8) AS binary) AS id,
	CAST(CONVERT(group_name USING utf8) AS binary) AS name,
	CAST(CONVERT(count_distinct_node USING utf8) AS binary) AS group_count
FROM $this->cache_db.{$this->cache->getNodeGroupsTable()}	
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
	CAST(CONVERT(`node_name` USING utf8) AS binary) AS name,
	CAST(CONVERT(`node_latitude` USING utf8) AS binary) AS latitude,
	CAST(CONVERT(`node_json_url` USING utf8) AS binary) AS json_url,
	CAST(CONVERT(`node_longitude` USING utf8) AS binary) AS longitude,
	CAST(CONVERT(groups.id USING utf8) AS binary) AS `group`,
	CAST(CONVERT(node_size USING utf8) AS binary) AS size,
	CAST(CONVERT(node_img USING utf8) AS binary) AS img,
	CAST(CONVERT(types.id USING utf8) AS binary) AS `type`,
	CAST(CONVERT(`node_id` USING utf8) AS binary) AS id,
	0 AS weight_sum,
	0 AS degree,
	CAST(CONVERT(nodes.id USING utf8) AS binary) AS my_index
FROM $this->cache_db.{$this->cache->getNodesTable()} AS nodes
LEFT JOIN $this->cache_db.{$this->cache->getNodeGroupsTable()} AS groups ON 
	groups.group_name =
    	node_group
LEFT JOIN $this->cache_db.{$this->cache->getNodeTypesTable()} AS types ON 
	types.node_type = 
    	nodes.node_type 
ORDER BY nodes.id ASC
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
            $nodes[(int) $this_row->my_index] = [
                'name' => $this_row->name,
                'short_name' => substr($this_row->name, 0, 50),
                'longitude' => $this_row->longitude,
                'latitiude' => $this_row->latitude,
		'json_url' => $this_row->json_url,
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

	//nodes are built, but we want to make sure that it turns into an array in the json rather than object..
	$nodes = array_values($nodes); //should return  a zero indexed array. not sure why this converts it to an array.. but it does....

        // Retrieve the links from the DB
        $links_sql = "SELECT * FROM $this->cache_db.`{$this->cache->getLinksTable()}`";
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
            FROM $this->cache_db.{$this->cache->getSummaryTable()}";

        $summary = [];
        $summary_result = ZermeloDatabase::connection($this->cache->getConnectionName())->select(DB::raw($summary_sql));
        foreach ($summary_result as $this_row) {
            $summary[][$this_row->summary_key] = $this_row->summary_value;
        }

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
