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
use CareSet\Zermelo\Reports\Graph\GraphGenerator;

class GraphApiController
{
    public function index( GraphReportRequest $request )
    {
        $report = $request->buildReport();
        $cache = new CachedGraphReport( $report );
        $generatorInterface = new GraphGenerator( $cache );
        return $generatorInterface->toJson();
    }
}
