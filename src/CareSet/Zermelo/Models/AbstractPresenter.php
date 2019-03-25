<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 7/5/18
 * Time: 1:04 PM
 */

namespace CareSet\Zermelo\Models;


abstract class AbstractPresenter
{
    protected $_report = null;
    protected $_token = null;

    public function __construct( ZermeloReport $report )
    {
        $this->_report = $report;
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
}