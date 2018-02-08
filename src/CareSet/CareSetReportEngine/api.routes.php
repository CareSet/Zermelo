<?php



Route::get('/CareSetReportSummary/{report_name}/{parameters?}', function($report_name,$parameters="")
{

    $Parameters = ($parameters=="")?[]:explode("/",$parameters);
    $Code = null;

    if(count($Parameters) > 0)
    {
        $Code = array_shift($Parameters);
    }

    try {
        $report = "App\\CareSetReports\\{$report_name}";
        $Report = new $report($Code,$Parameters);
    } catch(Exception $e)
    {
        abort(404);
    }

    $Controller = new \CareSet\CareSetReportEngine\Controllers\CareSetReportController;
    return $Controller->ReportModelSummaryJson($Report);

})->where(['parameters' => '.*']);



Route::get('/CareSetReport/{report_name}/{parameters?}', function($report_name,$parameters="")
{

    $Parameters = ($parameters=="")?[]:explode("/",$parameters);
    $Code = null;

    if(count($Parameters) > 0)
    {
        $Code = array_shift($Parameters);
    }

    try {
        $report = "App\\CareSetReports\\{$report_name}";
        $Report = new $report($Code,$Parameters);
    } catch(Exception $e)
    {
        abort(404);
    }

    $Controller = new \CareSet\CareSetReportEngine\Controllers\CareSetReportController;
    return $Controller->ReportModelJson($Report);

})->where(['parameters' => '.*']);

