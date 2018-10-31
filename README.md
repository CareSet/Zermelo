Zermelo Reporting Engine
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


Core Reporting features
------------------
* Write SQL, automatically get a web-based report
* Decorate each row in the report with links, buttons, JS and other bootstrap-based HTML goodness.
* Control the entire web report using a single PHP file, which contains SQL and decorations.
* GET/POST/JSON/URL parameters are all available as local functions/variables in the single file. This allows the SQL to be heavily modified based on the paramaters passed in to the specific report url.
* Automatically support server side data paging
* Any report data can be downloaded in CSV files
* Report automatically generates JSON data source that can be used as an API
* Supports Laravel's Blade templating engine out of the box

We have a [feature roadmap](FullFeature.md) if you want to see where we are going

Architecture
------------------

![Zermelo Data Flow Diagram](https://raw.githubusercontent.com/CareSet/Zermelo/master/documentation/Zermelo_Reporting_Engine_Design.png)


How to get started using it
-------------------------

### Prerequisites
You will need a modern LAMP server with at least php 7.2 and at leat Laravel 5.5
[Complete Prerequisites](documentation/Prerequisites.md)

### Installation

Look in [Basic Installation](documentation/BasicInstall.md) for complete installation instructions

For a quick start, assuming your Laravel instance already has access to the DB that it needs

    ```
    composer require careset/zermelo
    php artisan install:zermelo
    php artisan install:zermelobladetabular
    mkdir app/Reports
    ```
This will install and configure zermelo, and create an app/Reports for you to add reports too.

Next, you should test your routes...

```
    $ php artisan route:list | grep Zermelo
    |        | GET|HEAD | Zermelo/{report_name}/{parameters?}            |      | Closure |              |
    |        | GET|HEAD | api/Zermelo/{report_name}/{parameters?}        |      | Closure |              |
    |        | GET|HEAD | api/ZermeloSummary/{report_name}/{parameters?} |      | Closure |              |
```

And then check using your browser

``` 
    [base_url]/Zermelo/[ReportClassName]
```
### Running Example
We provide example reports, and the schema and data needed to run those reports. 
This is a good place to start if you are just exploring the system. Read, [Running the Examples](documentation/RunExample.md)


### Configuration Notes 
1. Edit the file `config/zermelo.php` to change core zermelo settings
1. Edit the file `config/zermelobladetabular.php` to change settings specific to zermelo blade tabular view package.
1. If your app already has an App\Reports namespace and directory, you can change the REPORT_NAMESPACE setting in 
config/zermelo.php to something else like "Zermelo" and then create an app/Zermelo directory 
to place your example report in. Note: you will also need to change the namespace of Northwind*Reports.php files to "namespace 
App\Zermelo;" if you change the REPORT_NAMESPACE.
1. If you ran these commands as root user, you'll have to change the ownership of the php files so they are readable
by the webserver.
1. If the reports don't run take a look at: `[project-root]/storage/logs/laravel.log` for errors

### Creating Your First Report
1. In order to get your first report, you need to create a report file. The easiest way to create an new report file
is to run: `php artisan make:zermelo [YourNewReportName]`
1. Edit the file `/app/Zermelo/YourNewReportName` You must fill in a reasonable GetSQL() function that returns either a 
single SQL text string, or an array of SQL text strings.
1. Point your browser to https://yourapp.example.com/Zermelo/YourNewReportName
1. Enjoy seeing your data in an automatically pagable [Datatables](https://datatables.net/) display!!
1. Various functions and constants in the report file can dramatically change how the report is displayed on the front end. Use them to change the reports (a good first hack is to use the MapRow function to link one report to another report)


### Example Report Model
To see full list of functions and variables, pleasse see the ZermeloReport model - 
https://github.com/CareSet/Zermelo/blob/master/src/CareSet/Zermelo/Models/ZermeloReport.php

```php
<?php

namespace App\Zermelo;
use CareSet\Zermelo\Models\ZermeloReport;

class ExampleReport extends ZermeloReport
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
    * Additional URI parameters are passed as 
    *	$this->getCode() - which will give the first url segment after the report name
    *   $this->getParameters() - which will give an array of every later url segment after the getCode value
    *   $this->getInputs() - which will give _GET parameters (etc?)
    **/
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
    
    	/*
		//this logic would ensure that every cell in the TABLE_NAME column, was converted to a link to 
		//a table drilldown report
		$table_name = $row['TABLE_NAME'];
		$row[''] = "<a href='/Zermelo/TableDrillDownReport/$table_name/'>$table_name</a>";
	
	*/
    
        return $row;
    }

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


### Why 'Zermelo'?
------------------
Zermelo has been developed by [CareSet Systems](https://careset.com) which provides extensive reporting on CMS, Medicare and Medicaid data. We developed Zermelo to make that task easier. CareSet systems uses Set Theory, SQL and Graph technology to datamine Medicare claims data. We chose the name "CareSet" for our company to highlight our data approach (our logo has a contains a graph, which we thought was a good compromise. In any case, we thought we should celebrate a famous set theory mathematician with our name. 

[Earnst Zermelo](https://en.wikipedia.org/wiki/Ernst_Zermelo) was one of the two independant mathematicians to posit the famous [Russell's Paradox](https://en.wikipedia.org/wiki/Russell%27s_paradox), the other being Russell. That paradox is the facinating question "Does a set that contains all sets that are not includes in themselves, contain itself". This paradox was a direct result of [Cantor](https://en.wikipedia.org/wiki/Georg_Cantor)'s work on Set Theory. All of which are critical chapters in the work on [Foundational Mathematics](https://en.wikipedia.org/wiki/Foundations_of_mathematics) shortly after the Turn of the 19th century.

So we figured Zermelo did not get enough credit for his independant development of the paradox (and his other work generally) and also, he has a cool name that is not really used much software projects, with the exception of previous work [automating table tennis tournaments](https://www.davidmarcus.com/Zermelo.htm) or [scheduling dutch students](https://www.zermelo.nl/), which are both different Zermelo software. But no one has a reporting engine with that name. 






