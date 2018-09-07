<?php
namespace CareSet\Zermelo\Models;

use CareSet\Zermelo\Interfaces\CacheInterface;
use CareSet\Zermelo\Interfaces\GeneratorInterface;
use CareSet\Zermelo\Models\ZermeloReport;
use Illuminate\Http\Request;

class ReportFactory
{
    /**
     * @param Request $request
     * @param $report_name
     * @param $parameter_string
     * @return ZermeloReport
     *
     * Build a ZermeloReport from a request
     */
    public static function build( Request $request, $report_name, $parameter_string ) : ZermeloReport
    {
        $parameters = ($parameter_string=="")?[]:explode("/", $parameter_string );

        // The code is the first parameter, saved on it's own for convenience
        $Code = null;
        if ( count( $parameters ) > 0) {
            $Code = array_shift($parameters);
        }

        // Request form input is non-cacheable input, aux parameters
        $request_form_input = json_decode(json_encode($request->all()),true);
        if ( !is_array( $request_form_input ) )  {
            $request_form_input = [];
        }

        // Get the report name and namespace
        $namespace = config("zermelo.REPORT_NAMESPACE");

        // Find the report Class
        if ( class_exists("$namespace\\{$report_name}\\{$report_name}" ) ) {
            $report = "$namespace\\{$report_name}\\{$report_name}";
        } else if ( class_exists("$namespace\\{$report_name}" ) ) {
            $report = "$namespace\\{$report_name}";
        }  else {
            throw new \Exception( "Report {$report_name} could not be found. Check the report class name in your URL and namespace." );
        }

        // Create a new instance of our report, loaded with request parameters
        $Report = new $report( $Code, $parameters, $request_form_input );
        if ( $Report instanceof ZermeloReport ) {

            $input_bolt = $Report->getParameter( 'data-option' );
            if ( $input_bolt == "" ) {
                $input_bolt = false;
            }

            $Report->SetBolt( $input_bolt );
        }

        return $Report;
    }
}
