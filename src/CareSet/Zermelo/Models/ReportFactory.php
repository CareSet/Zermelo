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
     * @param $parameters
     * @return ZermeloReport
     *
     * Build a ZermeloReport from a request
     */
    public static function build( Request $request ) : ZermeloReport
    {
        $report_name = $request->report_name;
        $parameters = $request->parameters;
        $namespace = config("zermelo.REPORT_NAMESPACE");

        $Parameters = ($parameters=="")?[]:explode("/",$parameters);
        $Code = null;


        $request_form_input = json_decode(json_encode($request->all()),true);

        if(!is_array($request_form_input)) $request_form_input = [];

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
            return false;
        }


        $Report = new $report($Code,$Parameters,$request_form_input);
        if ( $Report instanceof ZermeloReport ) {


            $Report->setRequestFormInput( $request->all() );

            $input_bolt = $Report->getParameter( 'data-option' );
            if ( $input_bolt == "" ) {
                $input_bolt = false;
            }

            $Report->SetBolt( $input_bolt );
        }

        return $Report;
    }
}
