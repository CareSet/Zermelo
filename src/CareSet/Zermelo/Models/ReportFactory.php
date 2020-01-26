<?php
namespace CareSet\Zermelo\Models;

use CareSet\Zermelo\Http\Requests\ZermeloRequest;
use CareSet\Zermelo\Interfaces\CacheInterface;
use CareSet\Zermelo\Services\SocketService;
use Illuminate\Http\Request;

class ReportFactory
{
    /**
     * @param Request $request
     * @param $report_name
     * @param $parameter_string
     * @return ZermeloReport
     *
     * Build a ZermeloReport from a report class and array of request parameters
     */
    public static function build( $reportClass, ZermeloRequest $request ) : ZermeloReport
    {
        $parameters = ( $request->parameters == "" ) ? [] : explode("/", $request->parameters );

        // The code is the first parameter, saved on it's own for convenience
        $code = null;
        if ( count( $parameters ) > 0) {
            $code = array_shift($parameters);
        }

        // Request form input is non-cacheable input, aux parameters
        $request_form_input = json_decode(json_encode($request->all()),true);
        if ( !is_array( $request_form_input ) )  {
            $request_form_input = [];
        }

        $reportObject = new $reportClass( $code, $parameters, $request_form_input);

        // Call GetSQL() in order to initilaize socket-wrench system for UI
        $reportObject->GetSQL();

        return $reportObject;
    }
}
