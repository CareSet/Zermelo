<?php

namespace App\Reports;
use CareSet\Zermelo\Reports\Tabular\AbstractTabularReport;

//note that we are specifically testing layout features with this report
//so we will not extend ParentTabularReport... 
//take a look at OverrideHeader for the real tests that this report undertakes
class TagsReport extends AbstractTabularReport
{

    /*
    * Get the Report Name
    */
    public function GetReportName(): string {
	return("Tag and Format Report");
    }

    /*
    * Get the Report Description, bootstrap styled html is OK
    */
    public function GetReportDescription(): ?string {
	$desc = "This is a report designed to test the tags and formatting functionality";
	return($desc);
    }

	/**
    * This is what builds the report. It will accept a SQL statement or an Array of sql statements.
    * Can be used in conjunction with Inputs to determine different output based on URI parameters
    * Additional URI parameters are passed as
    *	$this->getCode() - which will give the first url segment after the report name
    *   $this->getParameters() - which will give an array of every later url segment after the getCode value
    *   $this->getInputs() - which will give _GET parameters (etc?)
    **/
    public function GetSQL()
    {
	//replace with your own SQL
        $sql = "SELECT * FROM MyWind_aaa.tags_report";
    	return $sql;
    }

    /**
    * Each row content will be passed to MapRow.
    * Values and header names can be changed.
    * Columns cannot be added or removed
    * You can decorate fields with html, with bootstrap css styling
    *
    */
    public function MapRow(array $row, int $row_number) :array
    {
        return $row;
    }

    /**
    * If a column needs to be forced to a certain format (i.e. auto-detection gets it wrong), it can be changed here
    * Tags can also be applied to each header column
    */
    public function OverrideHeader(array &$format, array &$tags): void
    {
        $tags['field_to_bold_in_report_display'] = 	    ['BOLD'];
        $tags['field_to_hide_by_default'] = 		    ['HIDDEN'];
        $tags['field_to_italic_in_report_display'] = 	['ITALIC'];
        $tags['field_to_right_align_in_report'] = 	    ['RIGHT'];
        $tags['field_to_bolditalic_in_report_display'] = ['BOLD','ITALIC'];

        //How to set the format of the display
        $format['numeric_field'] = 			'NUMBER'; // Formats number in table using commas, and right-aligns
        $format['decimal_field'] = 			'DECIMAL'; // Formats decimal to 4 places, and right-aligns
        $format['currency_field'] = 	    'CURRENCY'; // adds $ or Eurosign and right align
        $format['percent_field'] = 			'PERCENT'; // adds % in the right place and right align
        $format['url_field'] = 			    'URL'; // auto-link using <a href='$url_field'>$url_field</a>
        $format['date_field'] = 			'DATE'; // future date display
        $format['datetime_field'] = 		'DATETIME'; //future date time display
        $format['time_field'] = 			'TIME'; // future time display
    }

 	/**
    * Header Format 'auto-detection' can be changed per report.
    * it is based on seeing the strings below in a field name... it will then assume it should be styled accordinly
    * By default, these are the column formats -
    * 	public $DETAIL     = ['Sentence'];
	* 	public $URL        = ['URL'];
	* 	public $CURRENCY   = ['Amt','Amount','Paid','Cost'];
	* 	public $NUMBER     = ['id','#','Num','Sum','Total','Cnt','Count'];
	* 	public $DECIMAL    = ['Avg','Average'];
	* 	public $PERCENT    = ['Percent','Ratio','Perentage'];
	*
	*	It detects the column by using 'word' matching, separated white spaces or _.
	*	Example: TABLE_ROWS - ['TABLE','ROWS']
	*	It will also check the full column name
    */
    public $NUMBER     = ['ROWS','AVG','LENGTH','DATA_FREE'];


    /*
    * By Default, any numeric field will have statistical information will be passed on. AVG/STD/MIN/MAX/SUM
    * Any Text column will have distinct count information passed on.
    * Any Date will have MIN/MAX/AVG
    * This field will add a "NO_SUMMARY" field to the column header to suggest the data not be displayed
    */
    public $SUGGEST_NO_SUMMARY = ['ID'];


	/**
    * Want to use your own blade file for the report front-end?
    * You can customize the report view based on the report
    * When this is set to null, the report will use the view defined in the configuration file.
    *
    */
	public $REPORT_VIEW = null;


	/**
    * Should we enable the cache on this table?
    * This will improve the performance of very large and complex queries by only running the SQL once and then storing 
    * the results in a dynamically creqted table in the _cache database.
    * But it also creates hard to debug update errors that are very confusing when changing GetSQL() contents. 
    */
	protected $CACHE_ENABLED = true;


	/**
    * How much time should pass (in seconds) before you update your _cache table for this report?
    * this only has an effect when isCacheEnabled is turned on. 
    */
	protected $HOW_LONG_TO_CACHE_IN_SECONDS = 600;


}
