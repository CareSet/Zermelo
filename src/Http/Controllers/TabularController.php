<?php

namespace CareSet\ZermeloBladeTabular\Http\Controllers;

use CareSet\Zermelo\Http\Controllers\AbstractWebController;
use CareSet\Zermelo\Interfaces\ZermeloReportInterface;
use CareSet\Zermelo\Models\Presenter;
use CareSet\ZermeloBladeTabular\TabularPresenter;

class TabularController extends AbstractWebController
{
    /**
     * @return \Illuminate\Config\Repository|mixed
     *
     * Get the view template
     */
    public  function getViewTemplate()
    {
        return config("zermelobladetabular.TABULAR_VIEW_TEMPLATE");
    }

    /**
     * @return string
     *
     * Specify the path to this report's API
     * This is a tabular report, so we'll use the tabular api prefix
     */
    public function getReportApiPrefix()
    {
        return tabular_api_prefix();
    }

    /**
     * @param $report
     * @return void
     *
     * Implement this method to do any custom configuration for this report,
     * like pushing variables onto the view that need to be there for EVERY report
     */
    public function onBeforeShown(ZermeloReportInterface $report)
    {
	//default to a sensible location for bootstrap in case the configuration value has not been set
        $bootstrap_css_location = asset(config('zermelobladetabular.BOOTSTRAP_CSS_LOCATION','/css/bootstrap.min.css'));
        $report->pushViewVariable('bootstrap_css_location', $bootstrap_css_location);
        $report->pushViewVariable('download_uri', $this->getDownloadUri($report));
        $report->pushViewVariable('report_uri', $this->getReportUri($report));
        $report->pushViewVariable('summary_uri', $this->getSummaryUri($report));
        $report->pushViewVariable('page_length', $this->getPageLength($report));
    }

    /**
     * @param $report
     * @return string
     *
     * Helper function for building the download URI
     */
    protected function getDownloadUri($report)
    {
        $parameterString = implode("/", $report->getMergedParameters() );
        $report_api_uri = "/{$this->getApiPrefix()}/{$this->getReportApiPrefix()}/{$report->uriKey()}/Download/{$parameterString}";
        return $report_api_uri;
    }

    protected function getReportUri($report)
    {
        $parameterString = implode("/", $report->getMergedParameters() );
        $report_api_uri = "/{$this->getApiPrefix()}/{$this->getReportApiPrefix()}/{$report->uriKey()}/{$parameterString}";
        return $report_api_uri;
    }

    protected function getSummaryUri($report)
    {
        $parameterString = implode("/", $report->getMergedParameters() );
        $summary_api_uri = "/{$this->getApiPrefix()}/{$this->getReportApiPrefix()}/{$report->uriKey()}/Summary/{$parameterString}";
        return $summary_api_uri;
    }

    protected function getPageLength($report)
    {
        $page_length =  $report->getParameter("length") ?: 50;
        return $page_length;
    }
}
