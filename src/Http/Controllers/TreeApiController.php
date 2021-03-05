<?php

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Requests\ZermeloRequest;
use CareSet\Zermelo\Reports\Tree\CachedTreeReport;
use CareSet\Zermelo\Reports\Tree\TreeReportGenerator;
use CareSet\Zermelo\Reports\Tree\TreeReportSummaryGenerator;

class TreeApiController
{
    public function index( ZermeloRequest $request )
    {
        $report = $request->buildReport();
        $cache = new CachedTreeReport( $report, zermelo_cache_db() );
        $generator = new TreeReportGenerator( $cache );
        return $generator->toJson();
    }

    public function summary( ZermeloRequest $request )
    {
        $report = $request->buildReport();
        // Wrap the report in cache
        $cache = new CachedTreeReport( $report, zermelo_cache_db() );
        $generator = new TreeReportSummaryGenerator( $cache );
        return $generator->toJson();
    }
}
