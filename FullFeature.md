Extended List of Zermello Reporting features
===================
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

