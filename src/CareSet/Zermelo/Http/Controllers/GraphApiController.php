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

        // We use a subclass of the Standard DatabaseCache to enhance the functionality
        // To cache, not only the "main" table, but the node and link tables as well
        $cache = new CachedGraphReport( $report, zermelo_cache_db() );
        $generatorInterface = new GraphGenerator( $cache );
        return $generatorInterface->toJson();
    }
}
