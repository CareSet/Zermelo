Zermelo Reporting Engine Running the Examples
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


## Running Example
We use a variation on the classic northwind database to test Zermelo features. We include the schema and data for those databases so that you 
can quickly get some example reports working...

There is a sample DB table and sample reports based on the Northwind customer database in the example directory of 
the Zermelo project. To run:

1. First, load the "MyWind" test databases. 
You can find the databases, and instructions for loading them here: [https://github.com/CareSet/MyWind_Test_Data](https://github.com/CareSet/MyWind_Test_Data)
These test databases work for both major CareSet projects: [DURC](https://github.com/CareSet/DURC) and Zermelo (this one).  

Load these databases and verify that they exist using your favorite database administration tool 

1. Then copy the example reports from [project-root]/vendor/careset/zermelo/examples/reports into your app/Reports directory. 
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

