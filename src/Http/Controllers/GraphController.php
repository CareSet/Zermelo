<?php

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Controllers\AbstractWebController;
use CareSet\Zermelo\Http\Requests\GraphReportRequest;
use CareSet\Zermelo\Interfaces\ZermeloReportInterface;

class GraphController extends AbstractWebController
{

    public  function getViewTemplate()
    {
        return config("zermelo.GRAPH_VIEW_TEMPLATE", "" );
    }

    public  function getReportApiPrefix()
    {
        return config('zermelo.GRAPH_URI_PREFIX');
    }

    /**
     * @param $report
     * @return void
     *
     * Push our graph URI variable onto the view
     */
    public function onBeforeShown(ZermeloReportInterface $report)
    {
        $bootstrap_css_location = asset(config('zermelo.BOOTSTRAP_CSS_LOCATION','/css/bootstrap.min.css'));
        $report->pushViewVariable('bootstrap_css_location', $bootstrap_css_location);
        $report->pushViewVariable('graph_uri', $this->getGraphUri($report));
    }

    /**
     * @param $report
     * @return string
     *
     * Helper to assemble the graph URI for the report
     */
    public function getGraphUri($report)
    {
        $parameterString = implode("/", $report->getMergedParameters() );
        $graph_api_uri = "/{$this->getApiPrefix()}/{$this->getReportApiPrefix()}/{$report->getClassName()}/{$parameterString}";
        $graph_api_uri = rtrim($graph_api_uri,'/'); //for when there is no parameterString
        return $graph_api_uri;
    }
}
