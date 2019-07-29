<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 6/20/18
 * Time: 11:42 AM
 */

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Requests\GraphReportRequest;
use CareSet\Zermelo\Reports\Graph\CachedGraphReport;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Reports\Graph\GraphGenerator;

class GraphApiController
{
    public function index( GraphReportRequest $request )
    {
        $report = $request->buildReport();
//	not sure why a different caching system than the tabular report was nessecary??
//        $cache = new CachedGraphReport( $report, zermelo_cache_db() );

        $cache = new DatabaseCache( $report, zermelo_cache_db() );
        $generatorInterface = new GraphGenerator( $cache );
        return $generatorInterface->toJson();
    }
}
