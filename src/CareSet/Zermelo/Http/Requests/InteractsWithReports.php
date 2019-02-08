<?php

namespace CareSet\Zermelo\Http\Requests;

use CareSet\Zermelo\Models\ReportFactory;
use CareSet\Zermelo\Zermelo;

trait InteractsWithReports
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Get the class name of the report being requested.
     *
     * @return mixed
     */
    public function reportClass()
    {
        // report_key is a request parameter defined by the route (we are inside a request object)
        return tap(Zermelo::reportForKey($this->report_key), function ($report) {
            abort_if(is_null($report), 404);
        });
    }

    /**
     * Get a new instance of the resource being requested.
     *
     * @return \CareSet\Zermelo\Models\ZermeloReport
     */
    public function buildReport()
    {
        // Get the report class by the report_key request parameter, or fail with 404 Not Found
        $reportClass = $this->reportClass();

        // Build a new instance of $reportClass using the found class, and THIS request
        // (this trait is for requests that interact with reports)
        return ReportFactory::build( $reportClass, $this );
    }
}
