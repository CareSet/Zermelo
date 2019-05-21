Zermelo Reporting Engine Running the Examples
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


## Running Example
We use a variation on the classic northwind database to test Zermelo features. We include the schema and data for those databases so that you 
can quickly get some example reports working...

To load the test databases the repo must be cloned into the directory next to the laravel project dir.
	Have the database root-enabled password ready.  It may be found in:
	/home/vagrant/code/zermelo-demo/.env
		defaults are:

	DB_USERNAME=homestead
	DB_PASSWORD=secret

If needed obtain the repo path from https://github.com/CareSet/MyWind_Test_Data

cd to vagrant@homestead:~/code

	$ git clone https://github.com/CareSet/MyWind_Test_Data.git

Assuming it cloned w/ no errors cd into the new repo dir:

    vagrant@homestead:~/code$ cd MyWind_Test_Data/

    vagrant@homestead:~/code/MyWind_Test_Data$ php load_databases.php


There is a sample DB table and sample reports based on the Northwind customer database in the example directory of 
the Zermelo project.

These test databases work for both major CareSet projects: [DURC](https://github.com/CareSet/DURC) and Zermelo (this one).  

1. Load these databases and verify that they exist using your favorite database administration tool 

2. Then copy the example reports from [project-root]/vendor/careset/zermelo/examples/reports into your app/Reports directory. 
You will need to create the app/Reports directory if it does not exist. From your project root:

    ```
    $ cp vendor/careset/zermelo/examples/reports/* app/Reports
    ```

Each example report can be accessed using the Zermelo report url. 
Assuming you have not changed the default urls in the zermelo configuration, you can load the reports in the following way

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

