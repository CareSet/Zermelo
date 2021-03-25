<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 5/2/19
 * Time: 12:33 PM
 */

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Requests\ZermeloRequest;
use CareSet\Zermelo\Interfaces\ZermeloReportInterface;
use CareSet\Zermelo\Models\ZermeloReport;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

abstract class AbstractWebController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param ZermeloReport $report
     * @return mixed
     *
     * Implemnt this method to do any modifications to the report at the controller level.
     * Any view variables you set here will be set on every report.
     */
    public abstract function onBeforeShown(ZermeloReportInterface $report);

    /**
     * @return mixed
     *
     * Implement this method to specify the blade view template to use
     */
    public abstract function getViewTemplate();

    /**
     * @return mixed
     *
     * Implement this method to specify the report URL path like /ZermeloCard or /ZermeloGraph
     */
    public abstract function getReportApiPrefix();


    /**
     * @return string
     *
     * Read the API prefix like `zapi` from the zermelo config fil
     */
    public function getApiPrefix()
    {
        return api_prefix();
    }

    /**
     * @param ZermeloRequest $request
     * @return null
     *
     * Default method for displaying a ZermeloReqest
     * This method builds the report, builds the presenter and returns the view
     */
    public function show(ZermeloRequest $request)
    {
        $report = $request->buildReport();
        $this->onBeforeShown($report);
        return $this->buildView($report);
    }

    /**
     * @return View
     *
     * Make a view by composing the report with necessary data from child controller
     */
    public function buildView(ZermeloReportInterface $report)
    {
        // Auth stuff
        $user = Auth::guard()->user();
        if ($user) {
            // Since this is a custom careset column on the database for JWT, make sure the property is set,
            if (isset($user->last_token)) {
                $report->setToken($user->last_token);
            }
        }

        // Get the overall Zermelo API prefix /zapi
        $report->pushViewVariable('api_prefix', $this->getApiPrefix());

        // Get the API prefix for this report's controller from child controller
        $report->pushViewVariable('report_api_prefix', $this->getReportApiPrefix());

        // Get the view template from the child controller
        $view_template = $this->getViewTemplate();

        // This function gets both view variables set on the report, and in the controller
        $view_varialbes = $report->getViewVariables();

        // Push all of our view variables on the template, including the report object itself
        $view_varialbes = array_merge($view_varialbes, ['report' => $report]);

        return view( $view_template, $view_varialbes );
    }
}
