<?php


Route::get('/CareSetReportSummary/{report_name}/{parameters?}', function($report_name,$parameters="")
{

    $namespace = config("caresetreportengine.REPORT_NAMESPACE");

    $Parameters = ($parameters=="")?[]:explode("/",$parameters);
    $Code = null;

    if(count($Parameters) > 0)
    {
        $Code = array_shift($Parameters);
    }

    if(class_exists("$namespace\\{$report_name}\\{$report_name}"))
    {
        $report = "$namespace\\{$report_name}\\{$report_name}";
    }
    else if(class_exists("$namespace\\{$report_name}"))
    {
        $report = "$namespace\\{$report_name}";
    }
    else
    {
        abort(404);
    }

    $Report = new $report($Code,$Parameters);
    $Controller = new CareSet\CareSetReportEngine\Controllers\CaresetReportController;
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

    if(class_exists("$namespace\\{$report_name}\\{$report_name}"))
    {
        $report = "$namespace\\{$report_name}\\{$report_name}";
    }
    else if(class_exists("$namespace\\{$report_name}"))
    {
        $report = "$namespace\\{$report_name}";
    }
    else
    {
        abort(404);
    }

    $Report = new $report($Code,$Parameters);
    $Controller = new CareSet\CareSetReportEngine\Controllers\CaresetReportController;
    return $Controller->ReportModelJson($Report);

})->where(['parameters' => '.*']);
