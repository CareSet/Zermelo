Zermelo Reporting Engine Controlling Caching
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


Controlling Caching
------------------

Caching is controlled using theses two variables on your subclass of ZermeloReport

public $CACHE_ENABLED = true; // Set to true to enable cache, false to disable cache

public $HOW_LONG_TO_CACHE_IN_SECONDS = 600; // How long in seconds to keep the report in cache

Cache Indicator
------------------

There is a cache icon in the report toolbar. A tooltip displays the time the cache was last generated and the time the cache will expire. Click the cache icon to engage a dropdown with a menu item "clear cache" to force regenerateion of the cache and reload the report.


Blue == Cache was generated last request

Red == You are working with cached data

Flashing Yellow == Cache is about to expire

Yellow == you are looking at expired data

