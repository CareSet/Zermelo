<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 7/5/18
 * Time: 12:58 PM
 */

namespace CareSet\Zermelo\Reports\Graph;


use CareSet\Zermelo\Models\AbstractPresenter;

class GraphPresenter extends AbstractPresenter
{
    private $_api_prefix = null;
    private $_graph_path = null;

    /**
     * Overrideable custom graph view template to use
     *
     * @var string
     */
    protected $GRAPH_VIEW = null;


    /**
     * getGraphView
     * Returns the $GRAPH_VIEW value
     *
     * @return string
     */
    public function getGraphView(): ?string
    {
        return $this->GRAPH_VIEW;
    }

    protected function getApiPrefix() : string
    {
        return $this->_api_prefix;
    }

    public function setApiPrefix( string $api_prefix )
    {
        $this->_api_prefix = $api_prefix;
    }

    public function getGraphPath() : string
    {
        return $this->_graph_path;
    }

    public function setGraphPath( string $graph_path )
    {
        $this->_graph_path = $graph_path;
    }


    public function getGraphUri()
    {
        $parameterString = implode("/", $this->_report->getMergedParameters() );
        $graph_api_uri = "/{$this->getApiPrefix()}/{$this->getGraphPath()}/{$this->_report->getClassName()}/{$parameterString}";
	$graph_api_uri = rtrim($graph_api_uri,'/'); //for when there is no parameterString
        return $graph_api_uri;
    }
}
