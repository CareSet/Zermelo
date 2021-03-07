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
     * By default, laravel uses the zapi prefix so that it doesn't conflict with existing APIs
     */
    'API_PREFIX'=>env("API_PREFIX","zapi"),

    /**
     * This is the prefix for the tabular API routes for retrieving data formatted
     * For jQuery DataTables
     */
    'TABULAR_API_PREFIX' => env("TABULAR_API_PREFIX","Zermelo"),

    /**
     * This is the prefix for the tabular API routes for retrieving data formatted
     * For jQuery DataTables
     */
    'TREE_API_PREFIX' => env("TREE_API_PREFIX","ZermeloTree"),

    /**
     * This is the prefix for the tabular API routes for retrieving data formatted
     * For D3 and other graphing toolkits
     */
    'GRAPH_API_PREFIX'=>env("GRAPH_API_PREFIX","ZermeloGraph"),

    /**
     * Determine if the 'TAGS' will be restricted to the valid TAGS or if they are just suggestions.
     * If RESTRICT_TAGS is set to true and a column is set to an invalid tag, an InvalidHeaderTagException will be thrown
     */
    'RESTRICT_TAGS'=>env("REPORT_STRICT_TAGS",true),

    // Any middleware you want to run on zermelo routes (ie: 'auth')
    'MIDDLEWARE' => ['api'],

    // Get the middleware for web routes that are in zermelo core, like the SQL pretty-printer
    'WEB_MIDDLEWARE' => ['web'],

    /**
     * The prefix for the web route that displays the doctrine sql-formatter view, which
     * lists the queries generated for the report that is specified after the prefix.
     *
     * The route is disabled by default for security reasons.
     *
     * The default middleware for this route is the web middleware.
     */
    'SQL_PRINT_PREFIX' => env("SQL_PREFIX","ZermeloSQL"),

    'SQL_PRINT_ENABLED' => env("SQL_PRINTER_ENABLED",false),

    'SQL_PRINT_VIEW_TEMPLATE' => env("SQL_PRINT_VIEW_TEMPLATE","Zermelo::layouts.sql_layout"),

    'BOOTSTRAP_CSS_LOCATION' => env("BOOTSTRAP_CSS_LOCATION","/vendor/CareSet/zermelo/core/bootstrap/bootstrap.min.css"),

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
     * The template the controller will use to render the report
     * This is used in WebController implementation of ControllerInterface@show method
     */
    "CARD_VIEW_TEMPLATE"=>env("CARD_VIEW_TEMPLATE","Zermelo::layouts.card_layout"),

    /**
     * Middleware on the card web routes
     */
    'CARD_MIDDLEWARE' => env("CARD_MIDDLEWARE", [ "web" ]),

    /**
     * Path where the Report display.
     * This is used in implementations of ControllerInterface@show method
     * Note: the API routes are auto generated with this same URI path with the api-prefixed to the url
     * /Zermelo/(ReportName) (see config/zermelo.php for api prefix setting)
     */
    'CARD_URI_PREFIX'=>env("CARD_URI_PREFIX","ZermeloCard"),

    /**
     * Path where the Report display.
     * This is used in implementations of ControllerInterface@show method
     * Note: the API routes are auto generated with this same URI path with the api-prefixed to the url
     * /ZermeloGraph/(ReportName) (see config/zermelo.php for api prefix setting)
     */
    'GRAPH_URI_PREFIX'=>env("GRAPH_URI_PREFIX","ZermeloGraph"),


    /**
     * Middleware on the graph web routes
     */
    'GRAPH_MIDDLEWARE' => env("MIDDLEWARE", [ "web" ]),

    /**
     * The template the controller will use to render the report
     * This is used in WebController implementation of ControllerInterface@show method
     */
    'GRAPH_VIEW_TEMPLATE'=>env("GRAPH_VIEW_TEMPLATE","Zermelo::layouts.d3graph_layout"),

    /**
     * Path where the Report display.
     * This is used in the route configuration in this module's ServiceProvider
     * /Zermelo/(ReportName)
     */
    'TABULAR_URI_PREFIX' => env("TABULAR_URI_PREFIX","Zermelo"),

    /**
     * Middleware on the tabular web routes
     */
    'TABULAR_MIDDLEWARE' => env("TABULAR_MIDDLEWARE", [ "web" ]),


    /**
     * The template the controller will use to render the report
     * This is used in WebController implementation of ControllerInterface@show method
     */
    "TABULAR_VIEW_TEMPLATE"=>env("TABULAR_VIEW_TEMPLATE","Zermelo::layouts.tabular_layout"),

    /**
     * The template the controller will use to render the report
     * This is used in WebController implementation of ControllerInterface@show method
     */
    "TREECARD_VIEW_TEMPLATE"=>env("VIEW_TEMPLATE","Zermelo::layouts.tree_card_layout"),

    /**
     * Middleware on the card web routes
     */
    'TREECARD_MIDDLEWARE' => env("MIDDLEWARE", [ "web" ]),

    /**
     * Path where the Report display.
     * This is used in implementations of ControllerInterface@show method
     * Note: the API routes are auto generated with this same URI path with the api-prefixed to the url
     * /Zermelo/(ReportName) (see config/zermelo.php for api prefix setting)
     */
    'TREECARD_URI_PREFIX'=>env("TREECARD_URI_PREFIX","ZermeloTreeCard"),

    /**
     * Database path where all the cache table will be stored.
     * This is set at installation and is not recommended to change.
     */
    'ZERMELO_CACHE_DB'=>env("ZERMELO_CACHE_DB","_zermelo_cache"),

    /**
     * Database path where configuration data will be stored, for sockets, etc
     */
    'ZERMELO_CONFIG_DB'=>env("ZERMELO_CONFIG_DB","_zermelo_config"),
];
