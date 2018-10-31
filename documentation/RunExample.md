Zermelo Reporting Engine Running the Examples
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


## Running Example
We use a variation on the classic northwind database to test Zermelo features. We include the schema and data for those databases so that you 
can quickly get some example reports working...

There is a sample DB table and sample reports based on the Northwind customer database in the example directory of 
the Zermelo project. To run:

1. Import the two northwind database files from [project-root]/vendor/careset/zermelo/example/data using mysql. These 
files will create two databases and their data. 

    ```
    $ sudo mysql -u root -p
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

1. Then copy the example reports from [project-root]/vendor/careset/zermelo/examples/reports into your app/Reports directory. 
You will need to create the app/Reports directory if it does not exist. From your project root:

    ```
    $ cp vendor/careset/zermelo/examples/reports/* app/Reports
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

