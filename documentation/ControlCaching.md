Zermelo Reporting Engine Controlling Caching
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


Controlling Caching
------------------

Caching is controlled using theses two variables on your subclass of ZermeloReport
```
public $CACHE_ENABLED = true; // Set to true to enable cache, false to disable cache
public $HOW_LONG_TO_CACHE_IN_SECONDS = 600; // How long in seconds to keep the report in cache
// note that $HOW_LONG_TO_CACHE_IN_SECONDS does nothing if $CACHE_ENABLED = false;
```

Cache Indicator
------------------

There is a cache icon in the report toolbar. A tooltip displays the time the cache was last generated and the time the cache will expire. Click the cache icon to engage a dropdown with a menu item "clear cache" to force regenerateion of the cache and reload the report.

The basic sequence for serving a report on the back end is: 

* SQL in the report is run, and a table in the \_zermelo cache directory holds the results.
* A request comes in and either the request is served from the cache, or the SQL is re-run. 
* The request will be re-run, if the cache is "expired" or if caching is turned off. 


Blue == The data you are seeing was loaded from a cache that was refreshed, immediately before it was sent to your browser

Red == The data you are seeing was loaded from the cache, that was not refreshed immediately before it was sent to your browser.

Yellow == The data you are seeing was loaded from the cache, and, if you asked it to, the server would now refresh the cache from the query. So if you want to load new data, just refresh the report

Flashing Yellow == Cache is about to expire, which means that the data was in the 'red' state, but is about to be in the 'yellow' state. 



