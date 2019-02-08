<?php

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Requests\TabularReportRequest;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Reports\Tabular\ReportGenerator;
use CareSet\Zermelo\Reports\Tabular\ReportSummaryGenerator;
use DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TabularApiController
{
    public function index( TabularReportRequest $request )
    {
        $report = $request->buildReport();
        $cache = new DatabaseCache( $report );
        $generator = new ReportGenerator( $cache );
        return $generator->toJson();
    }

    public function summary( TabularReportRequest $request )
    {
        $report = $request->buildReport();
        // Wrap the report in cache
        $cache = new DatabaseCache( $report );
        $generator = new ReportSummaryGenerator( $cache );
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
        $cache = new DatabaseCache( $report );
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
