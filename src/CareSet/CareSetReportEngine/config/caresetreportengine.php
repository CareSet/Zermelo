<?php

/*
 * This file is part of careset/caresetreportengine.
 */

return [

    /**
     * Namespace of the report where it will attempt to load from
     */
    'REPORT_NAMESPACE' =>env("REPORT_NAMESPACE","App\\CareSetReports"),

    /**
     * Path where the Report display.
     * This path should be inside the web route and points to CareSetReportController@ReportDisplay
     * /CareSetReport/(ReportName)
     */
    'URI_REPORT_PATH'=>env("REPORT_URI","/CareSetReport/"),
    

    /**
     * Path where the Report data can be retrieved from.
     * This path should be inside the api route and points to CareSetReportController@ReportModelJson
     */
    'URI_API_PATH'=>env("REPORT_API_URI","/api/CareSetReport/"),
    

    /**
     * Path where the Report header can be retrieve from.
     * This is use so it can display just the summary information for the report without retrieving the data
     * This path should be inside the api route and points to CareSetReportController@ReportModelSummaryJson
     */
	'URI_SUMMARY_PATH'=>env("REPORT_SUMMARY_URI","/api/CareSetReportSummary/"),
    

    /**
     * Database path where all the cache table will be stored.
     * Controller will attempt to create this database if it does not exists. This can throw an exception
     * if the user account does not have permission to create database.
     */
    'CACHE_DB'=>env("REPORT_CACHE_DB","_cache"),


    /**
     * Make the cache table presistent. If this value is set to false, The a TEMPORARY table will be used instead
     */
    'CACHABLE' =>env("REPORT_CACHABLE",true),

    /**
     * How old is the cache table before we need to override it.
     * In Minutes.
     * 0 = no timeout
     */
    'CACHE_TIMEOUT' =>env("REPORT_TIMEOUT", 0),

    /**
     * Should the engine auto index any 'subject' it detects in the report
     */
    "AUTO_INDEX" => env("REPORT_AUTO_INDEX",true),

    /**
     * Determine if the 'TAGS' will be restricted to the valid TAGS or if they are just suggestions.
     * If RESTRICT_TAGS is set to true and a column is set to an invalid tag, an InvalidHeaderTagException will be thrown
     */
    'RESTRICT_TAGS'=>env("REPORT_STRICT_TAGS",true),


    /**
     * List of valid tags to be used with RESTRICT_TAGS
     */
    'TAGS'=> [
                    'HIDDEN',
                    'BOLD',
                    'ITALIC',
                    'RIGHT'
    ],


    /**
     * The template the Report Engine will use to render the report
     * This will be called from CareSetReportController@ReportDisplay
     */
    "DEFAULT_TABULAR_TEMPLATE"=>env("REPORT_TEMPLATE","CareSetReportEngine.tabular"),
    "DEFAULT_GRAPH_TEMPLATE"=>env("REPORT_TEMPLATE","CareSetReportEngine.graph"),

];
