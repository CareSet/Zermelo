Zermelo Config File Documentation
===========================

Here are the variables in the documentation and what they do... 

* REPORT_NAMESPACE - what is the expected php namespace of the reports, this also dictates what directory things will live in. By default reports live in app/Reports and in the name space of App\Reports.
* API_PREFIX - we figure that you might already have something that lives under the /api of your url system.. so we use /zapi by default for all of the Zermelo API calls.. but this lets you change that..
* TABULAR_API_PREFIX - This is the place where you will find both the web UX and the api for the tabular reports. The default is just 'Zermelo' which means that https://yoursite.com/Zermelo/YourReportTabularNameHere/ will work out of the box. Note: the Card  reporting engine uses the same API as the tabular reports... but the UX url is https://yoursite.com/ZermeloCard/YourCardReportNameHere/
* TREEAPI_PREFIX - This is the place where you will find both the web UX and the api for the tree reports. The default is just 'ZermeloTree' which means that https://yoursite.com/ZermeloTree/YourTreeReportNameHere/ will work out of the box.
* GRAPH_API_PREFIX - This is the place where you will find both the web UX and the api for the graph reports. The default is just 'ZermeloGraph' which means that https://yoursite.com/ZermeloGraph/YourReportTabularNameHere/ will work out of the box.
* RESTRICT_TAGS - should we be strict in looking at formating tags
* MIDDLEWARE - add additional middleware for Zermelo to honor here... this is how you enable authentication
* TAGS - these are the formatting tags that should be used with RESTRICT_TAGS
* ZERMELO_CACHE_DB - this is where the Zeremelo DB cache will be held
* ZERMELO_CONFIG_DB - this is where the Zermelo configuration database lives.


