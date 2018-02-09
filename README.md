CareSetReportEngine
========

#### Installation
1. `composer require careset/caresetreportengine`
2. `php artisan vendor:publish --provider="CareSet\\CareSetReportEngine\\ServiceProvider"`

#### Configuration
1. `config/caresetreportengine.php`
> `REPORT_NAMESPACE` - The Report engine will attempt to load report model from this namespace. The report model MUST extends from `CareSet\CareSetReportEngine\Models\CareSetReport`



### Example Report Model
```php
<?php

namespace App\CareSetReports;
use CareSet\CareSetReportEngine\Models\CareSetReport;

class ExampleReport extends CareSetReport
{

    const REPORT_NAME 	= "Example Report Name";
    const DESCRIPTION 	= "Example Report Description";
 
 
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
    * This is what builds the report. It will accept a SQL statement or an Array of sql statements.
    * Can be used in conjunction with Inputs to determine different output based on URI parameters
    * Additional URI parameters are passed as $this->getCode() and $this->getParameters()
    *
    */
    public function GetSQL()
    {
        $sql = "SELECT * FROM information_schema.TABLES";
    	return $sql;
    }

    /**
    * Each row content will be passed to MapRow.
    * Values and header names can be changed.
    * Columns cannot be added or removed
    * 
    */
    public function MapRow(array $row) :array 
    {
        return $row;
    }

    /**
    * Column Headers will be auto detected using $DETAIL,$URL,$CURRENCY,$NUMBER,$DECIMAL,$PERCENT
    * If a column needs to be forced to a certain format, it can be changed here
    * Tags can also be applied to each header column
    */
    public function OverrideHeader(array &$format, array &$tags): void
    {
    $tags['TABLE_SCHEMA'] = ['BOLD'];
    }


    /*
    * Get the Report Name, by default it will fetch the const REPORT_NAME.
    * This can be overridden to custom return different Name based on Input
    */
    public function GetReportName(): string
    {
    return self::REPORT_NAME;
    }

    /*
    * Get the Report Description, by default it will fetch the const DESCRIPTION.
    * This can be overridden to custom return different description based on Input
    */
    public function getReportDescription(): ?string
    {
    return self::DESCRIPTION;
    }

}

```
