<?php

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Requests\TabularReportRequest;
use CareSet\Zermelo\Models\ZermeloMeta;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Reports\Tabular\ReportGenerator;
use CareSet\Zermelo\Reports\Tabular\ReportSummaryGenerator;
use DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TabularApiController extends AbstractController
{
    public function index( TabularReportRequest $request )
    {
error_log("In index controller 1");
        $report = $request->buildReport();
error_log("In index controller 2");
        $cache = new DatabaseCache( $report, zermelo_cache_db() );
error_log("In index controller 3");
        $generator = new ReportGenerator( $cache );
error_log("In index controller 4");
        return $generator->toJson();
    }

    public function summary( TabularReportRequest $request )
    {
        error_log("In summary controller 1");
        $report = $request->buildReport();
error_log("In summary controller 2");
        // Wrap the report in cache
        $cache = new DatabaseCache( $report, zermelo_cache_db() );
error_log("In summary controller 3");
        $generator = new ReportSummaryGenerator( $cache );
error_log("In summary controller 4");
        return $generator->toJson();
    }

    /**
     * Generate the download for the targeted report. This relies on the cached version of the ReportJSON
     * @param TabularReportRequest $request Tabular request for report
     * @return CSV download
     *
     */
    public function download( TabularReportRequest $request )
    {
        $report = $request->buildReport();
        $connectionName = zermelo_cache_db();
        $cache = new DatabaseCache( $report, $connectionName );
        $summaryGenerator = new ReportSummaryGenerator( $cache );
        $header = $summaryGenerator->runSummary();
        $header = array_map( function( $element ) {
            return $element['title'];
        }, $header );
        $reportGenerator = new ReportGenerator( $cache );
        $collection = $reportGenerator->getCollection();

        $response = new StreamedResponse( function() use ( $header, $collection ) {
            // Open output stream
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv( $handle, $header );

            // Get all users
            foreach ( $collection as $value ) {
                // Add a new row with data
                fputcsv( $handle, json_decode(json_encode($value), true) );
            }

            // Close the output stream
            fclose($handle);
        }, 200, [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$report->GetReportName().'.csv"',
            'Content-Type' => 'application/octet-stream',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public'
        ]);

        return $response;
    }
}
