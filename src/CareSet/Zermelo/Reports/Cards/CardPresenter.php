<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 7/5/18
 * Time: 1:03 PM
 */
namespace CareSet\Zermelo\Reports\Cards;

use CareSet\Zermelo\Models\AbstractPresenter;
use CareSet\Zermelo\Models\ZermeloReport;

class CardPresenter extends AbstractPresenter
{
    private $_api_prefix = null;
    private $_report_path = null;
    private $_summary_path = null;

    public function __construct( AbstractCardsReport $report )
    {
        parent::__construct( $report );
    }

    /**
     * $REPORT_VIEW
     * Overrideable custom report View template to use
     *
     * @var string
     */
    protected $REPORT_VIEW = null;


    /**
     * getReportView
     * Returns the $REPORT_VIEW value
     *
     * @return string
     */
    public function getReportView(): ?string
    {
        return $this->REPORT_VIEW;
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

    public function getReportUri()
    {
        $parameterString = implode("/", $this->_report->getMergedParameters() );
        $report_api_uri = "/{$this->getApiPrefix()}/{$this->getReportPath()}/{$this->_report->getClassName()}/{$parameterString}";
        return $report_api_uri;
    }

    public function getSummaryUri()
    {
        $parameterString = implode("/", $this->_report->getMergedParameters() );
        $summary_api_uri = "/{$this->getApiPrefix()}/{$this->getSummaryPath()}/{$this->_report->getClassName()}/{$parameterString}";
        return $summary_api_uri;
    }

    public function getPageLength()
    {
        $page_length =  $this->_report->getParameter("length") ?: null;
        return $page_length;
    }

}
