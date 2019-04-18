<?php

namespace App\Reports;
use CareSet\Zermelo\Reports\Tabular\AbstractTabularReport;

class NorthwindProductReport extends AbstractTabularReport
{

    protected $CACHE_ENABLED = false; //caching is advanced functionality, not on during demo
    protected $HOW_LONG_TO_CACHE_IN_SECONDS = 600; //should you want to turn it on, this will apply

    /*
    * Get the Report Name
    */
    public function GetReportName(): string {
	return('Zermelo Demo: Northwind Product Report');
    }

    /*
    * Get the Report Description, can be html
    */
    public function GetReportDescription(): ?string {
    	return('The list of all northwind products');
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
        $sql = "SELECT productCode, productName, standardCost, listPrice
                FROM northwind_model.product";
    	return $sql;
    }

    /**
    * Each row content will be passed to MapRow.
    * Values and header names can be changed.
    * Columns cannot be added or removed
    *
    */
    public function MapRow(array $row, int $row_number) :array
    {

    	/*
		//this logic would ensure that every cell in the TABLE_NAME column, was converted to a link to
		//a table drilldown report
		$table_name = $row['TABLE_NAME'];
		$row[''] = "<a href='/Zermelo/TableDrillDownReport/$table_name/'>$table_name</a>";

	*/

        return $row;
    }

 	/**
    * Header Format 'auto-detection' can be changed per report.
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
    * Can customize the report view based on the report
    * By default, use the view defined in the configuration file.
    *
    */
	public $REPORT_VIEW = null;



    /**
    * Column Headers will be auto detected using $DETAIL,$URL,$CURRENCY,$NUMBER,$DECIMAL,$PERCENT
    * If a column needs to be forced to a certain format, it can be changed here
    * Tags can also be applied to each header column
    */
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



}
