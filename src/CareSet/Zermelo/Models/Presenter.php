<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 7/5/18
 * Time: 1:04 PM
 */

namespace CareSet\Zermelo\Models;

class Presenter
{
    protected $_report = null;
    protected $_token = null;
    protected $_view_variables = [];

    public function __construct(ZermeloReport $report)
    {
        $this->_report = $report;
        $this->_view_variables = $report->getViewVariables();
    }

    public function getSocketwrenchUri()
    {
        $uri = api_prefix()."/socketwrenches/submit";
        return $uri;
    }

    public function getRequestFormInput()
    {
        return $this->_report->getRequestFormInput();
    }

    public function getReport()
    {
        return $this->_report;
    }

    public function getToken()
    {
        return $this->_token;
    }

    public function setToken( $token )
    {
        $this->_token = $token;
    }

    /**
     * @return array
     *
     * Get any variables set on the presenter (in the controller) or
     * from within the report using $report->pushViewVariable()
     */
    public function getViewVariables()
    {
        // Get any view varialbes set on the report
        $report_view_varialbes = $this->_report->getViewVariables();

        // Merge variables from the presenter and the report, with the report
        // taking precedence to override the presenter
        // https://www.php.net/manual/en/function.array-merge.php
        // If the input arrays have the same string keys, then the later value for
        // that key will overwrite the previous one
        $merged = array_merge($this->_view_variables, $report_view_varialbes);

        return $merged;
    }

    /**
     * @param $name
     * @param $value
     *
     * View variables are copied from the report at initilization of the presenter,
     * and can also be set in the controller via the presenter
     */
    public function pushViewVariable($name, $value)
    {
        $this->_view_variables[$name] = $value;
    }
}