<?php

/*
 * This file is part of careset/zermelo.
 */

return [

    /**
     * Namespace of the report where it will attempt to load from
     */
    'REPORT_NAMESPACE' =>env("REPORT_NAMESPACE","App\\Reports"),

    /**
     * If the api route has a prefix, use this prefix when pre-pend to the uri
     * By default, laravel uses the api prefix in their api.php file
     */
    'URI_API_PREFIX'=>env("API_PREFIX","api"),

    'prefixes' => [

    ],
    
    /**
     * Database path where all the cache table will be stored.
     * Controller will attempt to create this database if it does not exists. This can throw an exception
     * if the user account does not have permission to create database.
     */
    'CACHE_DB'=>env("REPORT_CACHE_DB","_cache"),


    /**
     * Should the reporting engine leverage a cache table? 
     * This greatly improves performance for long running reports, but can result in reports briefly showing out 
     * of date contents. 
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


];
