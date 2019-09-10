<?php

function api_prefix()
{
    $api_prefix = trim( config("zermelo.API_PREFIX"), "/ " );
    return $api_prefix;
}

function tree_api_prefix()
{
    $api_prefix = trim( config("zermelo.TREE_API_PREFIX"), "/ " );
    return $api_prefix;
}

function tabular_api_prefix()
{
    $api_prefix = trim( config("zermelo.TABULAR_API_PREFIX"), "/ " );
    return $api_prefix;
}

function graph_api_prefix()
{
    $api_prefix = trim( config("zermelo.GRAPH_API_PREFIX"), "/ " );
    return $api_prefix;
}

function zermelo_cache_db()
{
    $db = config("zermelo.ZERMELO_CACHE_DB" );
    if ( empty($db)) {
        info("Zermelo Cache DB not set in zermelo.php config file.");
    }
    return $db;
}

function zermelo_config_db()
{
    $db = config("zermelo.ZERMELO_CONFIG_DB" );
    if ( empty($db)) {
        info("Zermelo Config DB not set in zermelo.php config file.");
    }
    return $db;
}

function report_path()
{
    $reportNS = config("zermelo.REPORT_NAMESPACE" );
    $parts = explode("\\", $reportNS );
    return app_path($parts[count($parts)-1]);
}



