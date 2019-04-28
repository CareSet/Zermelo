<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 7/5/18
 * Time: 1:03 PM
 */
namespace CareSet\Zermelo\Reports\Tabular;

use CareSet\Zermelo\Models\AbstractPresenter;
use CareSet\Zermelo\Models\ZermeloReport;

class TabularPresenter extends AbstractPresenter
{
    private $_api_prefix = null;
    private $_report_path = null;
    private $_summary_path = null;

    public function __construct( ZermeloReport $report )
    {
        parent::__construct( $report );
    }

    protected function getApiPrefix() : string
    {
        return $this->_api_prefix;
    }

    public function setApiPrefix( string $api_prefix )
    {
        $this->_api_prefix = $api_prefix;
    }

    public function getReportPath() : string
    {
        return $this->_report_path;
    }

    public function setReportPath( string $report_path )
    {
        $this->_report_path = $report_path;
    }

    public function getSummaryPath() : string
    {
        return $this->_summary_path;
    }

    public function setSummaryPath( string $summary_path )
    {
        $this->_summary_path = $summary_path;
    }

    public function getDownloadUri()
    {
        $parameterString = implode("/", $this->_report->getMergedParameters() );
	//we also need to pass along _GET and _POST parameters by forcing them all into _GET...

	$s = '?'; //the first and only the first GET parameter is prefixed by this...
	$get_string = '';	
	foreach($this->_report->getInput() as $input_key => $input_value){
		$ue_input_value = urlencode($input_value); 
		$ue_input_key = urlencode($input_key); //should not need this.. but just to be careful..
		$get_string .= "$s$ue_input_key=$ue_input_value"; 	
		$s = '&';
	}//now $get_string should either have all of the _GET _POST parameters or be blank string

        $report_api_uri = "/{$this->getApiPrefix()}/{$this->getReportPath()}/{$this->_report->uriKey()}/Download/{$parameterString}$get_string";

        return $report_api_uri;
    }

    public function getReportUri()
    {
        $parameterString = implode("/", $this->_report->getMergedParameters() );
        $report_api_uri = "/{$this->getApiPrefix()}/{$this->getReportPath()}/{$this->_report->uriKey()}/{$parameterString}";
        return $report_api_uri;
    }

    public function getSummaryUri()
    {
        $parameterString = implode("/", $this->_report->getMergedParameters() );
        $summary_api_uri = "/{$this->getApiPrefix()}/{$this->getReportPath()}/{$this->_report->uriKey()}/Summary/{$parameterString}";
        return $summary_api_uri;
    }

    public function getPageLength()
    {
        $page_length =  $this->_report->getParameter("length") ?: null;
        return $page_length;
    }

}
