Zermelo Reporting Engine Basic Installation
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


## Basic Installation
1. Configure your database if you haven't already. In your project root, place your database parameters in .env or your app's config/database.php 
config. The database user will need CREATE TABLE permissions in order to create the \_zermelo database (or if you are 
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

1. From the command prompt at your laravel **project's root** install the following commands: 

    ```
    composer require careset/zermelo_installer
    php artisan zermelo:install
    ```
    
The installer pulls down the the zermelo package, which contains the API backend, and all avaialble view packages, 
such as zermelobladetabular and zermelobladecard. The installer will also move zermelo assets such as Javascript and 
CSS files to support the view packages into the public/vendor/CareSet directory.    
   
After running these commands, you will have a new app/Reports directory in your laravel project
where the Zermelo report classes will be placed. 

The installer will also create a _zermelo_cache databse and a _zermelo_config database. The cache
database retains a cached view of the query resulting from each report, and it's usage is configured
in the report class. The config database holds data for report configuration, such as the socket/wrench
varialbles that are injectable into report queries.


## Test your web routes (default):

You should now see 'Zermelo' routes in your Laravel instance

List your routes:
```
    $ php artisan route:list | grep Zermelo
    |        | GET|HEAD | Zermelo/{report_name}/{parameters?}            |      | Closure |              |
    |        | GET|HEAD | api/Zermelo/{report_name}/{parameters?}        |      | Closure |              |
    |        | GET|HEAD | api/ZermeloSummary/{report_name}/{parameters?} |      | Closure |              |
```


TROUBLESHOOTING
------------------

Please refer to the [Troubleshooting](documentation/Troubleshooting.md) guide. 


