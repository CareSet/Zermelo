<?php

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Requests\CardsReportRequest;
use CareSet\Zermelo\Http\Requests\ZermeloRequest;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Reports\Tabular\ReportGenerator;
use CareSet\Zermelo\Reports\Tabular\ReportSummaryGenerator;

class CardsApiController
{
    public function index( ZermeloRequest $request )
    {
        $report = $request->buildReport();
        $cache = new DatabaseCache( $report, zermelo_cache_db() );
        $generator = new ReportGenerator( $cache );
        return $generator->toJson();
    }

    public function summary( ZermeloRequest $request )
    {
        $report = $request->buildReport();
        // Wrap the report in cache
        $cache = new DatabaseCache( $report, zermelo_cache_db() );
        $generator = new ReportSummaryGenerator( $cache );
        return $generator->toJson();
    }
}
