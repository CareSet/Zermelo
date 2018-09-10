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
- PHP 7.1.+ installed, 7.2.+ preferred (required for nullable type declarations, and soon encrypted zip files)
- Composer Installed. See [Comopser Getting Started](https://getcomposer.org/)
- Server requirements for Laravel 5.6:
```
    PHP >= 7.1.3
    OpenSSL PHP Extension
    PDO PHP Extension
    Mbstring PHP Extension
    Tokenizer PHP Extension
    XML PHP Extension
```
- MYSQL server, and user with CREATE TABLE permissions

  Optionally you can use Laravel's Homestead VM and Vagrant to create a VM with all the correct dependencies. See [Laravel Homestead Installation](https://laravel.com/docs/5.6/homestead)
  
- Installed and functioning Laravel 5.6. See [Laravel 5.6 Installation Instructions](https://laravel.com/docs/5.6/installation)

  A good way to start is to use composer to insure you download correct verstion:
  ```
  composer create-project laravel/laravel zermelo-demo  "5.6.*" --prefer-dist
  ```
  Now rename the .env.example file to .env and generate a application key:
  ```
  php artisan key:generate
  ```  
### Basic Installation
1. From the command prompt at your laravel project's root install the following commands: 

```
composer require careset/zermelo
php artisan install:zermelo
composer require careset/zermelobladetabular
php artisan install:zermelobladetabular
mkdir app/Reports
```
    
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
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER ON `_cache`.* TO 'your_chosen_username'@'%';

```
### Configuration ( TDM NOTE: This is in the wrong place.  No need to talk about changing the settings at this level)
1. Edit the file `config/zermelo.php` to change core zermelo settings
2. Edit the file `config/zermelobladetabular.php` to change settings specific to zermelo blade tabular view package.

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
    $ cp vendor/careset/zermelo/examples/reports/* app/Reports
```

**NOTE** If your app already has an App\Reports namespace and directory, you can change the REPORT_NAMESPACE setting in 
config/zermelo.php to something else like "Zermelo" and then create an app/Zermelo directory 
to place your example report in. Note: you will also need to change the namespace of Northwind*Reports.php files to "namespace 
App\Zermelo;" if you change the REPORT_NAMESPACE.

**NOTE** If you ran these commands as root user, you'll have to change the ownership of the php files so they are readable
by the webserver.

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

### Errors
If the reports don't run take a look at: `[project-root]/storage/logs/laravel.log` for errors

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






