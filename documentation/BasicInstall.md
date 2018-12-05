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

1. From the command prompt at your laravel project's root install the following commands: 

    ```
    composer require careset/zermelo
    php artisan install:zermelo
    ```
    This will install the base reporting engine and tabular view package.
    ```
    php artisan install:zermelobladetabular
    ```
    This will create a zermelo directory in your resources directory containing blade view templates. This will also publish the configuration file to your app's config directory, and move assets (js, css) to public/vendor.

    ```
    mkdir app/Reports
    ```
   This will be the directory where your reports will be created. 
  

## Test your web routes (default):

You should now see 'Zermelo' routes in your Laravel instance

List your routes:
```
    $ php artisan route:list | grep Zermelo
    |        | GET|HEAD | Zermelo/{report_name}/{parameters?}            |      | Closure |              |
    |        | GET|HEAD | api/Zermelo/{report_name}/{parameters?}        |      | Closure |              |
    |        | GET|HEAD | api/ZermeloSummary/{report_name}/{parameters?} |      | Closure |              |
```

## Set up a sample reports to test browser

