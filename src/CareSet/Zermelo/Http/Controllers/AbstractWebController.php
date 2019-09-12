<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 5/2/19
 * Time: 12:33 PM
 */

namespace CareSet\Zermelo\Http\Controllers;

use CareSet\Zermelo\Http\Requests\ZermeloRequest;
use CareSet\Zermelo\Models\Presenter;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

abstract class AbstractWebController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
     * @param ZermeloRequest $request
     * @return null
     *
     * Default method for displaying a ZermeloReqest
     * This method builds the report, builds the presenter and returns the view
     */
    public function show(ZermeloRequest $request)
    {
        $report = $request->buildReport();
        $presenter = $this->buildPresenter($report);
        return $this->buildView($presenter);
    }

    /**
     * @param $report
     * @return Presenter
     *
     * This is the default method for building a presenter,
     * which is responsible for consolidating the required data/parameters onto the view
     * such as the API URLS, tokens, additional parameters
     */
    public function buildPresenter($report)
    {
        return new Presenter($report);
    }

    /**
     * @return Presenter
     *
     * Make a presenter by composing the report and the view
     */
    public function buildView(Presenter $presenter)
    {
        // Get the overall Zermelo API prefix /zapi
        $presenter->pushViewVariable('api_prefix', api_prefix());

        // Get the API prefix for this report's controller
        $presenter->pushViewVariable('report_api_prefix', $this->getReportApiPrefix());

        // Auth stuff
        $user = Auth::guard()->user();
        if ( $user ) {
            $presenter->setToken( $user->getRememberToken() );
        }

        // Get the view template from the child controller
        $view_template = $this->getViewTemplate();

        // Push all of our view variables on the template, including the report object and presenter itself
        $view_varialbes = $presenter->getViewVariables();
        $view_varialbes = array_merge($view_varialbes, ['presenter' => $presenter, 'report' => $presenter->getReport()]);

        return view( $view_template, $view_varialbes );
    }

    /**
     * @return string
     *
     * Read the API prefix like `zapi` from the zermelo config file
     */
    public function getApiPrefix()
    {
        return api_prefix();
    }
}
