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
* Supports Blade templating engine
* Uses datatables in front end, backend supports search and sorting features. Allows single front end webpage to browse reports with millions of rows of results. Allows the initial ording of the data to happen based on the ORDER BY part of the underlying SQL.
* Supports configurable database variables. Uses the Laravel configuration system to allow users to define which databases are used in for common SQL excercises.
* Supports notion of "brackets/bolts". A way to abstractly model SQL, so that the same SQL structure can run against an compabitle schema. This allows a user to choose which identically structured data source(s) to use as input to the report. (future)
* once a bracket/bolt has been specified by a user, that choice shoudl follow them as they change reports. 
* The bracket/bolt system should allow a user to meaningful select from several different data meta-sources. It should also allow for classes of meta-sources (i.e. all meta-sources from the same year of data, or meta-sources from the same family of patients, etc etc) and allow selection of data sources at that class of meta-source level.
* It also requires a admin GUI for managing the bracket/bolt options.  (future)
* Downloads should always be zip files, so that a "license.txt" or other files can be included, but ALSO so that there is a report_meta_data.json file that details which bolts (and other metadata) were used to build a given .csv file
* Supports an admin-user-only interface (admin mode) with extra juice for designing and maintaining report (future)
* In admin mode, display useful SQL errors, much the same way that laravel can be made to [use whoops for php errors](http://filp.github.io/whoops/) (future)
* SQL errors when not in admin mode should be handled gracefully, in a way that makes sense to the end user. (future)
* More generally, the back end should be able to run tests and check for pre-requsites in data before running a report, and communicate failing status (rather than a report in the wrong context) back to users. The communication should happen using a well-defined json-based error language, which should be well-displayed on the front end. (future)
* In admin mode the well-displayed error messages should have greater context, to allow for quick debugging. 
* Unclear how to enable "admin mode" but it will likely be using a facade of some kind, or perhaps based on the out-of-box laravel user modeling system. (future)
* Each report may have functions that return paramaters that are "correct for testing". This function should be used by a URL oriented report checker to verify that a report returns correctly given the parameters. (future)
* Each report may have functions that return paramaters that are "false for testing". To verify that incorrect input generates sensible errors rather than crashes (future)
* System crashes hitting JSON url should generate "I crashed json" rather than using the default laravel crash screen. 
* Develop an artisan command that will loop over all reports, and use curl (etc) to hit the url with the good/bad inputs that the report provides and verify that the JSON being returned is correct. (future)
* in admin mode, a link display what the incoming parameters were given to the report file, and the SQL that was the result of those inputs. This allows a report developer to precisely see what SQL is powering a data output, despite the various database/table abstraction features. (future)
* in admin mode, a link to submit a new issue for this report, in the github file that is reponsible for this report. Uses the ability for [github issue creation links to prepopulate](https://github.com/isaacs/github/issues/99) to correctly target the right report file in the repo. (future)
* in admin mode, a link to the JSON for the report, for convenience (future)
* Support token based API authentication (future)
* Supports Vue.js templating engine (future)
* A way to get a success/fail version of every report URL (using the API token) so that you can test the stability of specific suite of reports over time using application/network uptime engines like [Nagios](https://www.nagios.org/). (future)

Architecture
------------------

![Zermelo Data Flow Diagram](https://raw.githubusercontent.com/CareSet/Zermelo/master/documentation/Zermelo_Reporting_Engine_Design.png)


How to get started using it
-------------------------

### Prerequisites
- PHP 7.1.+ installed, 7.2.+ preferred (required for nullable type declarations, and soon encrypted zip files)
- Composer Installed. See [Comopser Getting Started](https://getcomposer.org/)
- Server requirements for Laravel 5.5:
```
    PHP >= 7.1.0
    OpenSSL PHP Extension
    PDO PHP Extension
    Mbstring PHP Extension
    Tokenizer PHP Extension
    XML PHP Extension
```
- MYSQL server, and user with CREATE TABLE permissions
- Installed and functioning Laravel 5.5. See [Laravel 5.5 Installation Instructions](https://laravel.com/docs/5.5/installation) 
  A good way to start is to use composer to insure you download correct verstion:
  `composer create-project laravel/laravel zermelo-demo  "5.5.*" --prefer-dist`
  
### Basic Installation
1. From the command prompt at your laravel project's root install the following commands: 

    ```composer require careset/zermelo``` 
    
    ```php artisan install:zermelo``` 
    
    ```composer require careset/zermelobladetabular```
    
    ```php artisan install:zermelobladetabular```
    
3. Configure your database if you haven't already. In your project root, place your database parameters in .env or your app's config/database.php 
config. The database user will need CREATE TABLE permissions in order to create the cache database (or if you are 
installing the example data.) The DB_DATABASE parameter is for the default database. If you are installing example data, and reports,
you can put 'northwind_data' for the DB_DATABASE. If you have an existing database, put that in the DB_DATABASE field. You should replace the username and password below with sensible values. If this is foreign to you, you should read [How to secure you MySQL installation](https://dev.mysql.com/doc/mysql-security-excerpt/5.7/en/security.html)

```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=northwind_data
    DB_USERNAME=your_chosen_username
    DB_PASSWORD=randomly_generate_a_password_and_put_it_here
```

4. Create the _cache database and give your database user access to. The SQL commands for this are:

```
CREATE DATABASE _cache;
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER ON `_cache`.* TO 'your_chosen_username'@'%'

```

### Configuration 
1. Edit the file `config/zermelo.php` to change core zermelo settings
2. Edit the file `config/zermelobladetabular.php` to change settings specific to zermelo blade tabular view package.


### Running Example
There is a sample DB table and sample reports based on the Northwind customer database in the example directory of 
the Zermelo project. To run:

1. Import the two northwind database files from [project-root]/vendor/careset/zermelo/example/data using mysql. These 
files will create two databases and their data. 

```
    $ mysql -u root -p
```
```
    myslq> source [project-root]/vendor/careset/zermelo/examples/data/northwind_model.sql
```
```
    myslq> source [project-root]/vendor/careset/zermelo/examples/data/northwind_data.sql
```
    
```
    mysql> show databases;
    +--------------------+
    | Database           |
    +--------------------+
    | information_schema |
    | mysql              |
    | northwind_data     |
    | northwind_model    |
    | performance_schema |
    | sys                |
    +--------------------+
    6 rows in set (0.00 sec)
```
    
2. Then copy the example reports from [project-root]/vendor/careset/zermelo/examples/reports into your app/Reports directory. 
You will need to create the app/Reports directory if it does not exist. From your project root:
```
    $ mkdir app/Reports
```
```
    $ cp vendor/careset/zermelo/examples/reports/* app/Reports
```

**NOTE** If your app already has an App\Reports namespace and directory, you can change the REPORT_NAMESPACE setting in 
config/zermelo.php to something else like "Zermelo" and then create an app/Zermelo directory 
to place your example report in. Note: you will also need to change the namespace of Northwind*Reports.php files to "namespace 
App\Zermelo;" if you change the REPORT_NAMESPACE.

**NOTE** If you ran these commands as root user, you'll have to change the ownership of the php files so they are readable
by the webserver.


### To access your web routes (default):

List your routes:
```
    $ php artisan route:list
    +--------+----------+------------------------------------------------+------+---------+--------------+
    | Domain | Method   | URI                                            | Name | Action  | Middleware   |
    +--------+----------+------------------------------------------------+------+---------+--------------+
    |        | GET|HEAD | /                                              |      | Closure | web          |
    |        | GET|HEAD | Zermelo/{report_name}/{parameters?}            |      | Closure |              |
    |        | GET|HEAD | api/Zermelo/{report_name}/{parameters?}        |      | Closure |              |
    |        | GET|HEAD | api/ZermeloSummary/{report_name}/{parameters?} |      | Closure |              |
    |        | GET|HEAD | api/user                                       |      | Closure | api,auth:api |
    +--------+----------+------------------------------------------------+------+---------+--------------+
```

Displays tabular view
``` 
    [base_url]/Zermelo/[ReportClassName]
```

Example Report tabular views
``` 
    [base_url]/Zermelo/NorthwindCustomerReport
```
``` 
    [base_url]/Zermelo/NorthwindOrderReport
```
``` 
    [base_url]/Zermelo/NorthwindProductReport
```

### Creating Your First Report
1. In order to get your first report, you need to create a report file. The easiest way to create an new report file
is to run:
```
    php artisan make:zermelo [YourNewReportName]
```
2. Edit the file `/app/Zermelo/YourNewReportName` You must fill in a reasonable GetSQL() function that returns either a 
single SQL text string, or an array of SQL text strings.
3. Point your browser to https://yourapp.example.com/Zermelo/YourNewReportName
4. Enjoy seeing your data in an automatically pagable [Datatables](https://datatables.net/) display!!
5. Various functions and constants in the report file can dramatically change how the report is displayed on the front end. Use them to change the reports (a good first hack is to use the MapRow function to link one report to another report)


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
Zermelo is developed by [CareSet Systems](https://careset.com) which provides extensive reporting on CMS, Medicare and Medicaid data. We developed Zermelo to make that task easier. CareSet systems uses Set Theory, SQL and Graph technology to datamine Medicare claims data. We chose the name "CareSet" for our company to highlight our data approach (our logo has a contains a graph, which we thought was a good compromise. In any case, we thought we should celebrate a famous set theory mathematician with our name. 

[Earnst Zermelo](https://en.wikipedia.org/wiki/Ernst_Zermelo) was one of the two independant mathematicians to posit the famous [Russell's Paradox](https://en.wikipedia.org/wiki/Russell%27s_paradox), the other being Russell. That paradox is the facinating question "Does a set that contains all sets that are not includes in themselves, contain itself". This paradox was a direct result of [Cantor](https://en.wikipedia.org/wiki/Georg_Cantor)'s work on Set Theory. All of which are critical chapters in the work on [Foundational Mathematics](https://en.wikipedia.org/wiki/Foundations_of_mathematics) shortly after the Turn of the 19th century.

So we figured Zermelo did not get enough credit for his independant development of the paradox (and his other work generally) and also, he has a cool name that is not really used much software projects, with the exception of previous work [automating table tennis tournaments](https://www.davidmarcus.com/Zermelo.htm) or [scheduling dutch students](https://www.zermelo.nl/), which are both different Zermelo software. But no one has a reporting engine with that name. 






