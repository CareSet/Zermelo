<?php

namespace CareSet\Zermelo\Reports\Graph;

use CareSet\Zermelo\Interfaces\CacheInterface;
use CareSet\Zermelo\Models\AbstractGenerator;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloDatabase;


class GraphGenerator extends AbstractGenerator
{
    protected $cache = null;

    public function __construct( DatabaseCache $cache )
    {
        $this->cache = $cache;
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
        $filter = $this->cache->getReport()->getInput('filter');
        if ($filter && is_array($filter)) {
            $associated_filter = [];
            foreach($filter as $f=>$item)
            {
                $field = key($item);
                $value = $item[$field];
                $associated_filter[$field] = $value;
            }

            $this->addFilter($associated_filter);
        }

        $orderBy = $this->cache->getReport()->getInput('order') ?? [];
        $associated_orderby = [];
        foreach ($orderBy as $order) {
            $orderKey = key($order);
            $direction = $order[$orderKey];
            $associated_orderby[$orderKey] = $direction;
        }
        $this->orderBy($associated_orderby);

        $node_types = [];
        foreach ( $this->cache->getNodeTypes() as $nt ) {
            if ( $nt['visible'] ) {
                $node_types[] = $nt[ 'id' ];
            }
        }

        $link_types = [];
        foreach ( $this->cache->getLinkTypes() as $lt ) {
            if ( $lt['visible'] ) {
                $link_types[] = $lt[ 'id' ];
            }
        }

        $nodes =  ZermeloDatabase::connection($this->cache->getConnectionName())->table($this->cache->getNodeTable())->select("id", "type", "value", "size", "sum_weight","degree")->whereIn('type',$node_types )->get();
        $links = ZermeloDatabase::connection($this->cache->getConnectionName())->table($this->cache->getLinkTable())->select("source", "target", "link_type", "weight")->whereIn('link_type',$link_types)->whereNotNull("source")->whereNotNull("target")->get();

        return [
            'node_types' => array_values($this->cache->getNodeTypes()),
            'link_types' => array_values($this->cache->getLinkTypes()),
            'links' => $links,
            'nodes' => $nodes,
            'cache_meta_generated_this_request' => $this->cache->getGeneratedThisRequest(),
            'cache_meta_last_generated' => $this->cache->getLastGenerated(),
            'cache_meta_expire_time' => $this->cache->getExpireTime(),
            'cache_meta_cache_enabled' => $this->cache->getReport()->isCacheEnabled()
        ];
    }
}
