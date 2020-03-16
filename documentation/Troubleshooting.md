Zermelo Reporting Engine Troubleshoting
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


## Installation

I am seeing an error during `php artisan zermelo:install` like this:
```
SQLSTATE[HY000] [2002] Connection refused (SQL: CREATE DATABASE IF NOT EXISTS `_zermelo_cache`;)

You may not have permission to the database `_zermelo_cache` to query its existence.
* `root` may have insufficient permissions and you may have to run the following command:
	GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES ON `_zermelo_cache`.* TO 'root'@'localhost';
```
* Check your laravel .env file in the project root. Verify that the host, port, username and password parameters are correct for
the mysql DB connection.
* If you are using XAMPP or MAMP, the mysql port may not be configured to the standard port number. The default port 
number for XAMPP is 3308. The default port number for MAMP is 8889.
* Check your mysql user table and make sure that you have the correct host. Sometimes, mysql can be configured in such a 
way that 'localhost' will work, but the default '127.0.0.1' will not. If your other parameters are correct, try changing
the '127.0.0.1' to 'localhost', or run the provided GRANT query to grant the proper permissions.

## Post Installation

My reports don't run, or I just see a white screen, or a blank screen. 
* Take a look at: `[project-root]/storage/logs/laravel.log` for errors

I'm seeing a 404 error when I browse to my report url.
* Check URL, use `php artisan route:list` to make sure your route is there
* Make sure your report file is in the proper directory in App and is properly namespaced.
* Make sure your report class is a subclass of ZermeloReport, or else it will not be picked up by the engine

