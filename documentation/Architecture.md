Zermelo Reporting Engine
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)


Architecture
------------------

![Zermelo Data Flow Diagram](https://raw.githubusercontent.com/CareSet/Zermelo/master/documentation/Zermelo_Reporting_Engine_Design.png)

Basically the way Zermelo works is to run your SQL against your data... then put it into a cache table (usually in the \_zermelo database)
Then it does its paging and sorting against that cached version of your data.  
