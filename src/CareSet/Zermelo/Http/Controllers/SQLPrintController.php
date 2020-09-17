<?php

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Controllers\AbstractWebController;
use CareSet\Zermelo\Http\Requests\CardsReportRequest;
use CareSet\Zermelo\Interfaces\ZermeloReportInterface;
use CareSet\Zermelo\Models\ZermeloReport;
use Doctrine\SqlFormatter\SqlFormatter;

class SQLPrintController extends AbstractWebController
{
    /**
     * @return \Illuminate\Config\Repository|mixed
     *
     * Get the view template
     */
    public  function getViewTemplate()
    {
        return config("zermelo.SQL_PRINT_VIEW_TEMPLATE");
    }

    /**
     * @return string
     *
     * Specify the path to this report's API
     * This report uses the tabular api prefix
     */
    public function getReportApiPrefix()
    {
        return tabular_api_prefix();
    }

    /**
     * @param $report
     *
     * Build presenter and push our required varialbes for this web view
     */
    public function onBeforeShown(ZermeloReportInterface $report)
    {
        // before we show the report SQL, make sure SQL printing is enabled,
        // If not, throw an error
        if (!$report->isSQLPrintEnabled()) {
            abort(403, 'SQL Printing Is Not Enabled.');
        }

        $bootstrap_css_location = asset(config('zermelo.BOOTSTRAP_CSS_LOCATION','/css/bootstrap.min.css'));
        $report->pushViewVariable('bootstrap_css_location', $bootstrap_css_location);
        $report->pushViewVariable('report_uri', $this->getReportUri($report));
        $report->pushViewVariable('summary_uri', $this->getSummaryUri($report));

        // Use the SQL Formatter to push a formatted sql string to view
        $report_sql = $report->GetSQL();
        if (is_array($report_sql)) {

            // If the report SQL is an array, we have some more work to do to show formatting
            $formatted_sql = "";
            $cnt = 0;
            foreach ($report_sql as $sql_part) {
                $formatted_sql .= "<div><h2>[{$cnt}]</h2><br>" . (new SqlFormatter())->format($sql_part) . "</div>";
                $cnt++;
            }
        } else {
            $formatted_sql = (new SqlFormatter())->format($report_sql);
        }
        $report->pushViewVariable('formatted_sql', $formatted_sql);
    }

    /**
     * @param $report
     * @return string
     *
     * Protected method, specific to this controller, to build the report URI (though as of now it's the same as tabular)
     */
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
}
