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

    /**
     * Should the engine auto index any 'subject' it detects in the report
     */
    "AUTO_INDEX" => env("REPORT_AUTO_INDEX",true),

    /**
     * Determine if the 'TAGS' will be restricted to the valid TAGS or if they are just suggestions.
     * If RESTRICT_TAGS is set to true and a column is set to an invalid tag, an InvalidHeaderTagException will be thrown
     */
    'RESTRICT_TAGS'=>env("REPORT_STRICT_TAGS",true),

    // Any middleware you want to run on zermelo routes (ie: 'auth')
    'MIDDLEWARE' => [],

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
     * Database path where all the cache table will be stored.
     * This is set at installation and is not recommended to change.
     */
    'ZERMELO_DB'=>env("ZERMELO_DB","_zermelo"),
];
