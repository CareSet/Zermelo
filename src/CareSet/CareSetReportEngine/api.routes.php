<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/CareSetReportSummary/{report_name}/{parameters?}', function($report_name,$parameters="")
{

    $Parameters = ($parameters=="")?[]:explode("/",$parameters);
    $Code = null;

    if(count($Parameters) > 0)
    {
        $Code = array_shift($Parameters);
    }

    try {
        $report = "App\\CareSetReports\\{$report_name}\\{$report_name}";
        $Report = new $report($Code,$Parameters);
    } catch(Exception $e)
    {
        abort(404);
    }

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

    try {
        $report = "App\\CareSetReports\\{$report_name}\\{$report_name}";
        $Report = new $report($Code,$Parameters);
    } catch(Exception $e)
    {
        abort(404);
    }

    $Controller = new CareSet\CareSetReportEngine\Controllers\CaresetReportController;
    return $Controller->ReportModelJson($Report);

})->where(['parameters' => '.*']);
