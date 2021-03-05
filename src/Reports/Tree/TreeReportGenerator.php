<?php

namespace CareSet\Zermelo\Reports\Tree;

use CareSet\Zermelo\Interfaces\CacheInterface;
use CareSet\Zermelo\Models\AbstractGenerator;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloDatabase;
use DB;

class TreeReportGenerator extends AbstractGenerator
{
    protected $cache = null;
    protected $report = null;
    protected $cache_db = '_zermelo_cache'; //TODO this should be set in config
    protected $cache_table = 'will be changed';

    public function __construct(CachedTreeReport $cache)
    {
        $this->cache = $cache;
        $this->report = $cache->getReport();
	$this->cache_db = '_zermelo_cache'; //more hard-coding.
	$this->cache_table = $cache->getTableName();
	
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

	$select_sql = "
SELECT 
	root,
	root_url,
	branch,
	branch_url,
	leaf,
	leaf_url
FROM $this->cache_db.$this->cache_table
";

	$results = ZermeloDatabase::connection($this->cache->getConnectionName())->select(DB::raw($select_sql));

	$url_lookup = [];
	$tree = [];
	foreach($results as $this_row){
		if(strlen($this_row->root_url) >  0){
			$url_lookup[$this_row->root] = $this_row->root_url;
		}
		if(strlen($this_row->branch_url) > 0){
			$url_lookup["$this_row->root$this_row->branch"] = $this_row->branch_url;
		}
		if(is_null($this_row->leaf)){
			$tree[$this_row->root][$this_row->branch] = null;
		}else{
			$tree[$this_row->root][$this_row->branch][] = $this_row->leaf;
			if(strlen($this_row->leaf_url) > 0){
				$url_lookup["$this_row->root$this_row->branch$this_row->leaf"] = $this_row->leaf_url;
			}
		}	
	}

	$return_me = [];
//uncomment these for debugging..
//	$return_me['tree'] =$tree;
//	$return_me['url_lookup'] = $url_lookup;
//here is basically what I am going for..
/*
	[
		{
			label: 'something',
			url: 'a_url',
			sub_tree: [
				
			]
		}
	]
*/
	$data = [];
	foreach($tree as $root_node => $sub_node){
		$tmp = [];
		$tmp['label'] = $root_node;
		if(isset($url_lookup[$root_node])){
			$tmp['url'] = $url_lookup[$root_node];
		}
		
		if(is_array($sub_node)){
			$tmp['sub_tree'] = [];	
			foreach($sub_node as $branch_node => $leaf_node){
				$sub_tmp = [];
				$sub_tmp['label'] = $branch_node;
				if(isset($url_lookup["$root_node$branch_node"])){
					$sub_tmp['url'] = $url_lookup["$root_node$branch_node"];
				}
				if(is_array($leaf_node)){
					//this is the typical three-layer case..
					$sub_tmp['sub_tree'] = [];
					foreach($leaf_node as $leaf_label){
						$sub_sub_tmp = [];
						$sub_sub_tmp['label'] = $leaf_label;
						if(isset($url_lookup["$root_node$branch_node$leaf_label"])){
							$sub_sub_tmp['url'] = $url_lookup["$root_node$branch_node$leaf_label"];
						}
						$sub_tmp['sub_tree'][] = $sub_sub_tmp;
					}
				}else{
					//this is branch=leaf
					//do nothing..
				}
				$tmp['sub_tree'][] = $sub_tmp;
			}
		}else{
			//this is root=leaf..
			//do nothing for now...
		}
		$data[] = $tmp;
	}

	$return_me['data'] = $data;

	return($return_me);

    }


}
