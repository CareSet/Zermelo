Zermelo Reporting Engine
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


Core Reporting features
------------------
* Write SQL, automatically get a web-based report
* Decorate each row in the report with links, buttons, JS and other bootstrap-based HTML goodness.
* Control the entire web report using a single PHP file, which contains SQL and web interface decorations.
* GET/POST/JSON/URL parameters are all available as local functions/variables in the single file. This allows the SQL to be heavily modified based on the paramaters passed in to the specific report url.
* Automatically support server side data paging, allows engine to report against very large databases
* Any report data can be downloaded in CSV file(s)
* Report automatically generates JSON data source that can be used as an API
* Supports Laravel's Blade templating engine out of the box (with more effort supports any front end templating engine).

We have a [feature roadmap](FullFeature.md) if you want to see where we are going and an extended list of Zermello Reporting features.

ScreenShot that explains everything
--------------------------
![Zermelo Data Flow Diagram](https://raw.githubusercontent.com/CareSet/Zermelo/master/documentation/ZermeloScreenShot.png)



Architecture
------------------

Read the [Architecture diagram](documentation/Architecture.md)
Zermelo is doing the hard work of translating "mere SQL" into something that can be consistently and performantly loaded into a single browser session. 
Zermelo understands how to push the hard work to the MariaDB/MySQL server, ensuring that the browser gets its data in dribbles. The backend is having to do a huge amount of work in order make that happen. 

Some queries will legitimately take hours for the backend to run, even when the resulting data is only a few hundred rows of results. In order to support these heavy loads, Zermelo understands how to cache results. 
It always caches the results, but for most queries, it always refreshes the cache on every browser call. 

You, the user, get to control how this works. Look at the [Controlling the Cache](documentation/ControlCaching.md) documentation to see how.


How to get started using it
-------------------------

### Prerequisites
You will need a modern LAMP server with at least php 7.2 and at least Laravel 5.5
[Complete Prerequisites](documentation/Prerequisites.md)
Once the prerequisites are completed you should be able to check URLs on host system's browser at URL: homestead.test

### Installation
Look in [Basic Installation](documentation/BasicInstall.md) for complete installation instructions and database setup.

For a quick start, assuming your Laravel instance already has access to the DB that it needs

```bash
    $ composer require careset/zermelo
    $ composer require careset/zermelobladetabular
    $ php artisan install:zermelo
    $ php artisan install:zermelobladetabular
```
This will install and configure zermelo, and create an app/Reports for you to add reports too.

Next, you should test your routes...

```
    $ php artisan route:list | grep Zermelo
|        | GET|HEAD | Zermelo/{report_key}                                 |
|        | GET|HEAD | ZermeloCard/{report_key}                             |
|        | GET|HEAD | ZermeloGraph/{report_key}                            |
|        | GET|HEAD | api/Zermelo/{report_key}/Download/{parameters?}      |
|        | GET|HEAD | api/Zermelo/{report_key}/Summary/{parameters?}       |
|        | GET|HEAD | api/Zermelo/{report_key}/{parameters?}               | 
|        | GET|HEAD | api/ZermeloGraph/{report_key}/Download/{parameters?} |
|        | GET|HEAD | api/ZermeloGraph/{report_key}/{parameters?}          |
```

### Running Example
We provide example reports, and the schema and data needed to run those reports. 
This is a good place to start if you are just exploring the system. Read, [Running the Examples](documentation/RunExample.md)


### Configuration Notes 
1. Edit the file `config/zermelo.php` to change core zermelo settings
1. Edit the file `config/zermelobladetabular.php` to change settings specific to zermelo blade tabular view package.
1. If your app already has an App\Reports namespace and directory, you can change the REPORT\_NAMESPACE setting in 
config/zermelo.php to something else like "Zermelo" and then create an app/Zermelo directory 
to place your example report in. Note: you will also need to change the namespace of Northwind\*Reports.php files to "namespace 
App\Zermelo;" if you change the REPORT\_NAMESPACE.
1. If you ran these commands as root user, you'll have to change the ownership of the php files so they are readable
by the webserver.
1. If the reports don't run take a look at: `[project-root]/storage/logs/laravel.log` for errors

### Creating Your First Report
1. In order to get your first report, you need to create a report file. The easiest way to create an new report file
is to run: 

`php artisan make:zermelo [YourNewReportName]`

To understand what this does, take a look at the example report model below.

2. Edit the new file `/app/Zermelo/YourNewReportName` You must fill in a reasonable GetSQL() function that returns either a 
single SQL text string, or an array of SQL text strings.
3. Point your browser to https://yourapp.example.com/Zermelo/YourNewReportName
4. Enjoy seeing your data in an automatically pagable [Datatables](https://datatables.net/) display!!
5. Various functions and constants in the report file can dramatically change how the report is displayed on the front end. Use them to change the reports (a good first hack is to use the MapRow function to link one report to another report)


### Example Report Model
To see full list of functions and variables, please see the ZermeloReport model - 
https://github.com/CareSet/Zermelo/blob/master/src/CareSet/Zermelo/Models/ZermeloReport.php

```php

<?php

namespace App\Reports;
use CareSet\Zermelo\Reports\Tabular\AbstractTabularReport;

class ExampleReport extends AbstractTabularReport
{

    /*
    * Get the Report Name
    */
    public function GetReportName(): string {
        return("Enter your report name here");
    }

    /*
    * Get the Report Description, bootstrap styled html is OK
    */
    public function GetReportDescription(): ?string {
	$desc = "This is your report description <b> HTML is fine here </b>";
        return($desc);
    }

        /**
    * This is what builds the report. It will accept a SQL statement or an Array of sql statements.
    * Can be used in conjunction with Inputs to determine different output based on URI parameters
    * Additional URI parameters are passed as
    *   $this->getCode() - which will give the first url segment after the report name
    *   $this->getParameters() - which will give an array of every later url segment after the getCode value
    *   $this->getInput() - which will give _GET parameters (etc?)
    * 	$this->setInput($key,$new_value) - a way to override _GET parameters (i.e. for initializing a sort for instance)
    * 		For instance $this->setInput('order',[0 => ['order_by_me' => 'asc']]); will order the report, to start by the order_by_me column ASC. 
    *		This replicates what is being passed from the front end data tables to the backend to make sorting work.. 
    **/
    public function GetSQL()
    { 
        //replace with your own SQL
        $sql = "SELECT * FROM information_schema.TABLES";
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

        /*
                //this logic would ensure that every cell in the TABLE_NAME column, was converted to a link to
                //a table drilldown report
                $table_name = $row['TABLE_NAME'];

                $row['TABLE_NAME'] = "Gotta Love Those Row Decorations: $table_name";

                //this will make table name a link to another report
                //$row['TABLE_NAME'] = "<a href='/Zermelo/TableDrillDownReport/$table_name/'>$table_name</a>";

                //this will do the same thing, but styling the link as a bootstrap button.
                //$row['TABLE_NAME'] = "<a class='btn btn-primary btn-sm' href='/Zermelo/TableDrillDownReport/$table_name/'>$table_name</a>";
        */

        return $row;
    }

    /**
    * If a column needs to be forced to a certain format (i.e.ear auto-detection gets it wrong), it can be changed here
    * Tags can also be applied to each header column
    */
    public function OverrideHeader(array &$format, array &$tags): void
    {
    	//$tags['field_to_bold_in_report_display'] = 	['BOLD'];
        //$tags['field_to_hide_by_default'] = 		['HIDDEN'];
        //$tags['field_to_italic_in_report_display'] = 	['ITALIC'];
        //$tags['field_to_right_align_in_report'] = 	['RIGHT'];

        //How to set the format of the display
        //$format['numeric_field'] = 			'NUMBER'; // Formats number in table using commas, and right-aligns
        //$format['decimal_field'] = 			'DECIMAL'; // Formats decimal to 4 places, and right-aligns
        //$format['currency_field'] = 	    'CURRENCY'; // adds $ or Eurosign and right align
        //$format['percent_field'] = 			'PERCENT'; // adds % in the right place and right align
        //$format['url_field'] = 			    'URL'; // auto-link using <a href='$url_field'>$url_field</a>
        //$format['date_field'] = 			'DATE'; // future date display
        //$format['datetime_field'] = 		'DATETIME'; //future date time display
        //$format['time_field'] = 			'TIME'; // future time display
        //$format['row_summary_field'] =		['DETAIL']; // this field will be shown with a + sign in the column. 
                                        //pressing the plus will create a new row in the table
                                        //that shows the actual contents of this column.
    }

        /**
    * Header Format 'auto-detection' can be changed per report.
    * it is based on seeing the strings below in a field name... it will then assume it should be styled accordingly
    * So it a column label is 'very_good_num' or 'this num' will be matched by 'num' but 'number' will not work.
    * so it is matched on ignore case on a column name segment, not on substring...
    * By default, these are the column formats -
    *   public $DETAIL     = ['Sentence'];
        *       public $URL        = ['URL'];
        *       public $CURRENCY   = ['Amt','Amount','Paid','Cost'];
        *       public $NUMBER     = ['id','#','Num','Sum','Total','Cnt','Count'];
        *       public $DECIMAL    = ['Avg','Average'];
        *       public $PERCENT    = ['Percent','Ratio','Perentage'];
        *
        *       It detects the column by using 'word' matching, separated white spaces or _.
        *       Example: TABLE_ROWS - ['TABLE','ROWS']
        *       It will also check the full column name
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


    /*
    * Get Indexes for cache table
    * Because results are saved to a cache table, and then exported from there later searching, using the front end... can be very slow
    * This returns an array of Index commands that will be run against the cache table
    * because we do not know the name of the cache table in advance, these index commands must use the string '{{_CACHE_TABLE_}}' instead of
    * the name of a specific table...
    */
    public function GetIndexSQL(): ?array {

                $index_sql = [
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`COLUMN_NAME`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`TABLE_NAME`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`database_name`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD PRIMARY KEY( `database_name`, `TABLE_NAME`, `COLUMN_NAME`); "
                        ];
// you can uncomment this line to enable the default report to automatically index the resulting cached table
//                return($index_sql);

                //returning null here results in no indexing happening on the cached results table
                return(null);

    }

    /**
    * If the cache is not enabled, then every time the page is reloaded the entire report is re-processed and put into the cache table
    * So if you want to just run the report one time, and then load subsequent data from the cache, set this to return 'true';
    */
   public function isCacheEnabled(){
        return(false);
   }

    /**
    * This function does nothing if isCacheEnabled is returning false
    * But if the cache is enabled, then this will detail how long the report will be reloaded from the cache before the cache is regenerated by re-running the report SQL
    */
   public function howLongToCacheInSeconds(){
        return(1200); //twenty minutes by default
   }


}



```

### TROUBLESHOOTING
------------------

I'm seeing a 404 error when I browse to my report url.
* Check URL, use `php artisan route:list` to make sure your route is there
* Make sure your report file is in the proper directory in App and is properly namespaced.
* Make sure your report class is a subclass of ZermeloReport, or else it will not be picked up by the engine

### Why 'Zermelo'?
------------------
Zermelo has been developed by [CareSet Systems](https://careset.com) which provides extensive reporting on CMS, Medicare and Medicaid data. We developed Zermelo to make that task easier. CareSet systems uses Set Theory, SQL and Graph technology to datamine Medicare claims data. We chose the name "CareSet" for our company to highlight our data approach (our logo contains a graph and a 'set' of nodes, which we thought was a good illustration of our analytical approach. In any case, because of our focus on Set-theory approaches to data analytics we thought we should celebrate a famous set theory mathematician with the names of our Open Source projects. 

[Earnst Zermelo](https://en.wikipedia.org/wiki/Ernst_Zermelo) was one of the two independant mathematicians to posit the famous [Russell's Paradox](https://en.wikipedia.org/wiki/Russell%27s_paradox), the other being Russell. That paradox is the facinating question "Does a set that contains all sets that are not includes in themselves, contain itself". This paradox was a direct result of [Cantor](https://en.wikipedia.org/wiki/Georg_Cantor)'s work on Set Theory. All of which are critical chapters in the work on [Foundational Mathematics](https://en.wikipedia.org/wiki/Foundations_of_mathematics) shortly after the Turn of the 19th century.

So we figured Zermelo did not get enough credit for his independant development of the paradox (and his other work generally) and also, he has a cool name that is not really used much by software projects, with the exception of previous work [automating table tennis tournaments](https://www.davidmarcus.com/Zermelo.htm) or [scheduling dutch students](https://www.zermelo.nl/), which are both different Zermelo software solutions from this project.  But so far, no one has a reporting engine with this name, so we jumped at the opportunity to celebrate Zermelo's contribution to mathematics and data analysis by naming our php reporting engine after him! 






