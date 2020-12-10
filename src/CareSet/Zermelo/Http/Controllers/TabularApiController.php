<?php

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Requests\ZermeloRequest;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Reports\Tabular\ReportGenerator;
use CareSet\Zermelo\Reports\Tabular\ReportSummaryGenerator;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TabularApiController extends AbstractApiController
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

    /**
     * Generate the download for the targeted report. This relies on the cached version of the ReportJSON
     * @param ZermeloRequest $request Tabular request for report
     * @return CSV download
     *
     */
    public function download( ZermeloRequest $request )
    {
        // Type can be either 'csv' or 'excel' and we default to excel (shouldn't have to)
        $fileType = $request->get('download_file_type', 'excel');
        $report = $request->buildReport();
        $connectionName = zermelo_cache_db();
        $cache = new DatabaseCache( $report, $connectionName );
        $summaryGenerator = new ReportSummaryGenerator( $cache );
        $header = $summaryGenerator->runSummary();
        $header = array_map( function( $element ) {
            // Replace spaces with '_' in the header
            return preg_replace('/\s+/', '_', $element['title']);
        }, $header );
        $reportGenerator = new ReportGenerator( $cache );
        $collection = $reportGenerator->getCollection();

        // File name download should include MD5 from the contents of getCode #48
        if ($report->getCode()) {
            $filename = $report->GetReportName() . '-'.$report->getCode();
        } else {
            $filename = $report->GetReportName();
        }

        if ($fileType === 'csv') {
            $filename .= '.csv';
            return $this->csvResponse($filename, $header, $collection);
        } else {
            $filename .= '.xlsx';
            return $this->excelResponse($filename, $header, $collection);
        }
    }

    protected function csvResponse($filename, $header, $collection)
    {
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
            'Content-Disposition' => 'attachment; filename="'.urlencode($filename).'"',
            'Content-Type' => 'application/octet-stream',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public'
        ]);

        return $response;
    }

    protected function excelResponse($filename, $header, $collection)
    {
        $response = new StreamedResponse( function() use ($header, $collection) {

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 0, $l = sizeof($header); $i < $l; $i++) {
                $sheet->setCellValueByColumnAndRow($i + 1, 1, $header[$i]);
            }

            for ($i = 0, $l = sizeof($collection); $i < $l; $i++) { // row $i
                $j = 0;
                foreach ($collection[$i] as $k => $v) { // column $j
                    $sheet->setCellValueByColumnAndRow($j + 1, ($i + 1 + 1), $v);
                    $j++;
                }
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.urlencode($filename).'"',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public'
        ]);

        return $response;
    }
}
