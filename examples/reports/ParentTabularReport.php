<?php
/*
	If you want to ensure that each report has consistent contents you can create an intermediate report class to inherit from
	like this one!

	This can make it easier to change things like caching settings, for all of your reports at once!!
*/

namespace App\Reports;
use CareSet\Zermelo\Reports\Tabular\AbstractTabularReport;

class ParentTabularReport extends AbstractTabularReport
{




	//this will create a consistent menu...
        function getMenu(){

$menu_html = "
<nav class='navbar navbar-expand-lg navbar-light bg-light'>
    <a class='navbar-brand' href='/'>Zermelo Test Report Menu</a>
    <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarSupportedContent' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>
        <span class='navbar-toggler-icon'></span>
    </button>
    <div class='collapse navbar-collapse' id='navbarSupportedContent'>
        <ul class='navbar-nav mr-auto'>
            <li class='nav-item'>
               <a class='nav-link waves-effect waves-light' href='/Zermelo/NorthwindCustomerReport'>Customer Report</a>
            </li>
            <li class='nav-item'>
                <a class='nav-link waves-effect waves-light' href='/Zermelo/NorthwindOrderReport'>Order Report</a>
            </li>
            <li class='nav-item'>
                <a class='nav-link waves-effect waves-light' href='/Zermelo/NorthwindProductReport'>Product Report</a>
            </li>
        </ul>

    </div>
</nav>
";

        return($menu_html);

}

	//we want to have the same header override on all reports
    public function OverrideHeader(array &$format, array &$tags): void
    {
    	//$tags['field_to_bold_in_report_display'] = 	['BOLD'];
	//$tags['field_to_hide_by_default'] = 		['HIDDEN'];
	//$tags['field_to_italic_in_report_display'] = 	['ITALIC'];
	//$tags['field_to_right_align_in_report'] = 	['RIGHT'];

	//How to set the format of the display
	//$format['numeric_field'] = 			['NUMBER']; //TODO what does this do?
	//$format['decimal_field'] = 			['DECIMAL']; //TODO what does this do?
	//$format['currency_field'] = 			['CURRENCY']; //adds $ or Eurosign and right align
	//$format['percent_field'] = 			['PERCENT']; //adds % in the right place and right align
	//$format['url_field'] = 			['URL']; //auto-link using <a href='$url_field'>$url_field</a>
	//$format['numeric_field'] = 			['NUMBER']; //TODO what does this do?
	//$format['date_field'] = 			['DATE']; //future date display
	//$format['datetime_field'] = 			['DATETIME']; //future date time display
	//$format['time_field'] = 			['TIME']; //future time display
    }

    /**
	//accept the defaults on all of these formatting questions
     	*	public $DETAIL     = ['Sentence'];
	* 	public $URL        = ['URL'];
	* 	public $CURRENCY   = ['Amt','Amount','Paid','Cost'];
	* 	public $NUMBER     = ['id','#','Num','Sum','Total','Cnt','Count'];
	* 	public $DECIMAL    = ['Avg','Average'];
	* 	public $PERCENT    = ['Percent','Ratio','Perentage'];
    */


    /*
	//do not summarize the ID field.
    */
    public $SUGGEST_NO_SUMMARY = ['ID'];


	/**
		//use the default view blade
    */
	public $REPORT_VIEW = null;


	/**
	//turn off the cache on all reports
    */
   public function isCacheEnabled(){
	return(false);
   }

	/**
	//this does not make a difference since, we are turning off the cache for all child reports
	//but we keep the default for good measure.
    */
   public function howLongToCacheInSeconds(){
	return(600); //ten minutes by default
   }
   


}
